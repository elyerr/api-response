<?php

namespace Test\Assets;

use Elyerr\ApiExtend\Assets\Asset;
use PHPUnit\Framework\TestCase;

class AssetTest extends TestCase
{
    public function testGenerateUniqueCode()
    {
        $asset = new class {
            use Asset;
        };

        $code = $asset->generateUniqueCode();
        $this->assertNotEmpty($code);
    }

    public function testIsDifferent()
    {
        $asset = new class {
            use Asset;
        };

        // Prueba la lógica de is_diferent aquí
        $this->assertTrue($asset->is_diferent('old_value', 'new_value'));
        $this->assertFalse($asset->is_diferent('value', 'value'));
    }

    public function testFormatDate()
    {
        $asset = new class {
            use Asset;
        };

        $formattedDate = $asset->format_date("2023-01-25 12:00:00");

        $this->assertEquals("2023-01-25 12:00:00", $formattedDate);
    }

    public function testVerifyTimeIsBeetwen()
    {
        $asset = new class {
            use Asset;
        };

        $this->assertFalse($asset->verify_time_is_betweem("2023-12-12", "2023-12-25"));
        $this->assertTrue($asset->verify_time_is_betweem("2023-10-04", "2023-10-25"));

    }

    public function testChangeIndex()
    {
        $asset = new class {
            use Asset;
        };

        $index = $asset->changeIndex("user.0.name");
        $this->assertEquals("user.*.name", $index);
    }

    public function testAddString()
    {
        $asset = new class {
            use Asset;
        };

        $file = sys_get_temp_dir() . "/testFile.txt";

        $asset->addString($file, 1, "\ntest 1\ntest 2\ntest 3"); 
 
        $content = file_get_contents($file);
        $this->assertStringContainsString("test 2", $content);
 
        unlink($file);

    }

}
