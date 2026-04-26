<?php

namespace Elyerr\ApiResponse\Exceptions;

use Exception;
use Throwable;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Elyerr\ApiResponse\Assets\JsonResponser;

class ReportError extends Exception
{
    use JsonResponser;

    /**
     * message
     * @var string
     */
    public $message;

    /**
     * code
     * @var
     */
    public $code;

    public function __construct($message, $code)
    {
        $this->message = $message;
        $this->code = $code;
    }

    /**
     * Render exception
     * @param mixed $request
     * @return mixed|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function render($request)
    {
        $user = Auth::user();
        $traceId = (string) Str::uuid();
        $route = $request->route();
        $previous = $this->getPrevious();

        $logData = [
            'trace_id' => $traceId,
            'timestamp' => now()->toIso8601String(),
            'application' => [
                'env' => app()->environment(),
                'debug' => config('app.debug'),
                'url' => config('app.url'),
            ],
            'exception' => [
                'class' => static::class,
                'message' => $this->message,
                'code' => $this->code,
                'file' => $this->getFile(),
                'line' => $this->getLine(),
                'trace' => $this->getTrace(),
                'trace_as_string' => $this->getTraceAsString(),
                'previous' => $this->formatPreviousException($previous),
            ],
            'actor' => [
                'auth_check' => Auth::check(),
                'guard' => Auth::getDefaultDriver(),
                'user_id' => $user?->id,
                'user_email' => $user?->email,
                'user_type' => $user ? get_class($user) : null,
                'user_identifier' => $user?->getAuthIdentifier(),
            ],
            'request' => [
                'id' => $request->headers->get('X-Request-Id')
                    ?? $request->headers->get('X-Correlation-Id')
                    ?? $traceId,
                'method' => $request->method(),
                'scheme' => $request->getScheme(),
                'host' => $request->getHost(),
                'port' => $request->getPort(),
                'path' => $request->path(),
                'url' => $request->fullUrl(),
                'full_url' => $request->fullUrl(),
                'route_name' => optional($route)->getName(),
                'route_action' => optional($route)->getActionName(),
                'route_uri' => optional($route)->uri(),
                'controller' => optional($route)->getActionName(),
                'middleware' => $route?->gatherMiddleware(),
                'query' => $this->sanitizeForLog($request->query()),
                'payload' => $this->sanitizeForLog($request->all()),
                'files' => $this->extractFiles($request->allFiles()),
                'expects_json' => $request->expectsJson(),
                'wants_json' => $request->wantsJson(),
                'ajax' => $request->ajax(),
                'secure' => $request->isSecure(),
                'fingerprint' => method_exists($request, 'fingerprint') && $route ? $request->fingerprint() : null,
            ],
            'network' => [
                'ip' => $request->ip(),
                'ips' => $request->ips(),
                'client_ip' => $request->getClientIp(),
                'forwarded_for' => $request->headers->get('X-Forwarded-For'),
                'real_ip' => $request->headers->get('X-Real-IP'),
                'cf_connecting_ip' => $request->headers->get('CF-Connecting-IP'),
                'user_agent' => $request->userAgent(),
                'referer' => $request->headers->get('referer'),
                'origin' => $request->headers->get('origin'),
                'remote_addr' => $request->server('REMOTE_ADDR'),
                'server_addr' => $request->server('SERVER_ADDR'),
                'server_name' => $request->server('SERVER_NAME'),
            ],
            'session' => [
                'has_session' => $request->hasSession(),
                'session_id' => $request->hasSession() ? $request->session()->getId() : null,
            ],
            'headers' => $this->sanitizeForLog($request->headers->all()),
            'server' => $this->sanitizeForLog([
                'server_software' => $request->server('SERVER_SOFTWARE'),
                'server_protocol' => $request->server('SERVER_PROTOCOL'),
                'request_time' => $request->server('REQUEST_TIME'),
                'request_time_float' => $request->server('REQUEST_TIME_FLOAT'),
                'https' => $request->server('HTTPS'),
                'remote_port' => $request->server('REMOTE_PORT'),
                'server_port' => $request->server('SERVER_PORT'),
            ]),
            'context' => [
                'php_version' => PHP_VERSION,
                'sapi' => PHP_SAPI,
            ],
        ];

        Log::channel(config('logging.default'))->error('Exception captured', $logData);

        if ($request->wantsJson()) {
            return $this->message($this->message, $this->code);
        }

        abort($this->code, $this->message);
    }

    protected function sanitizeForLog(mixed $data): mixed
    {
        if (!is_array($data)) {
            return $data;
        }

        $sensitiveKeys = [
            'password',
            'password_confirmation',
            'current_password',
            'token',
            'access_token',
            'refresh_token',
            'id_token',
            'authorization',
            'cookie',
            'set-cookie',
            'client_secret',
            'secret',
            'api_key',
            'x-api-key',
        ];

        $sanitized = [];

        foreach ($data as $key => $value) {
            $normalizedKey = is_string($key) ? Str::lower($key) : $key;
            $isSensitive = is_string($normalizedKey)
                && collect($sensitiveKeys)->contains(fn($sensitiveKey) => Str::contains($normalizedKey, $sensitiveKey));

            if ($isSensitive) {
                $sanitized[$key] = '[REDACTED]';
                continue;
            }

            if (is_array($value)) {
                $sanitized[$key] = $this->sanitizeForLog($value);
                continue;
            }

            $sanitized[$key] = is_string($value) && strlen($value) > 4000
                ? Str::limit($value, 4000, '...[truncated]')
                : $value;
        }

        return $sanitized;
    }

    protected function extractFiles(array $files): array
    {
        return collect($files)->map(function ($file) {
            if (is_array($file)) {
                return $this->extractFiles($file);
            }

            return [
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getClientMimeType(),
                'size' => $file->getSize(),
                'error' => $file->getError(),
            ];
        })->toArray();
    }

    protected function formatPreviousException(?Throwable $exception): ?array
    {
        if (!$exception) {
            return null;
        }

        return [
            'class' => get_class($exception),
            'message' => $exception->getMessage(),
            'code' => $exception->getCode(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace_as_string' => $exception->getTraceAsString(),
        ];
    }
}
