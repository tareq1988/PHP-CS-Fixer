<?php

/*
 * This file is part of PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace PhpCsFixer\Tests\Fixer\Comment;

use PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException;
use PhpCsFixer\Tests\Test\AbstractFixerWithAliasedOptionsTestCase;
use PhpCsFixer\WhitespacesFixerConfig;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Comment\HeaderCommentFixer
 */
final class HeaderCommentFixerTest extends AbstractFixerWithAliasedOptionsTestCase
{
    /**
     * @param string $expected
     * @param string $input
     *
     * @dataProvider provideFixCases
     */
    public function testFix(array $configuration, $expected, $input)
    {
        $this->configureFixerWithAliasedOptions($configuration);

        $this->doTest($expected, $input);
    }

    public function provideFixCases()
    {
        return [
            [
                ['header' => ''],
                '<?php

$a;',
                '<?php

/**
 * new
 */
$a;',
            ],
            [
                [
                    'header' => 'tmp',
                    'location' => 'after_declare_strict',
                ],
                '<?php
declare(strict_types=1);

/*
 * tmp
 */

namespace A\B;

echo 1;',
                '<?php
declare(strict_types=1);namespace A\B;

echo 1;',
            ],
            [
                [
                    'header' => 'tmp',
                    'location' => 'after_declare_strict',
                    'separate' => 'bottom',
                    'comment_type' => 'PHPDoc',
                ],
                '<?php
declare(strict_types=1);
/**
 * tmp
 */

namespace A\B;

echo 1;',
                '<?php
declare(strict_types=1);

namespace A\B;

echo 1;',
            ],
            [
                [
                    'header' => 'tmp',
                    'location' => 'after_open',
                ],
                '<?php

/*
 * tmp
 */

declare(strict_types=1);

namespace A\B;

echo 1;',
                '<?php
declare(strict_types=1);

namespace A\B;

echo 1;',
            ],
            [
                [
                    'header' => 'new',
                    'comment_type' => 'comment',
                ],
                '<?php

/*
 * new
 */
                ',
                '<?php
                    /** test */
                ',
            ],
            [
                [
                    'header' => 'new',
                    'comment_type' => 'PHPDoc',
                ],
                '<?php

/**
 * new
 */
                ',
                '<?php
                    /* test */
                ',
            ],
            [
                [
                    'header' => 'def',
                    'comment_type' => 'PHPDoc',
                ],
                '<?php

/**
 * def
 */
',
                '<?php
',
            ],
            [
                ['header' => 'xyz'],
                '<?php

/*
 * xyz
 */

    $b;',
                '<?php
    $b;',
            ],
            [
                [
                    'header' => 'xyz123',
                    'separate' => 'none',
                ],
                '<?php
/*
 * xyz123
 */
    $a;',
                '<?php
    $a;',
            ],
            [
                [
                    'header' => 'abc',
                    'comment_type' => 'PHPDoc',
                ],
                '<?php

/**
 * abc
 */

$c;',
                '<?php
$c;',
            ],
            [
                [
                    'header' => 'ghi',
                    'separate' => 'both',
                ],
                '<?php

/*
 * ghi
 */

$d;',
                '<?php
$d;',
            ],
            [
                [
                    'header' => 'ghi',
                    'separate' => 'top',
                ],
                '<?php

/*
 * ghi
 */
$d;',
                '<?php
$d;',
            ],
            [
                [
                    'header' => 'tmp',
                    'location' => 'after_declare_strict',
                ],
                '<?php

/*
 * tmp
 */

declare(ticks=1);

echo 1;',
                '<?php
declare(ticks=1);

echo 1;',
            ],
            [
                ['header' => 'Foo'],
                '<?php

/*
 * Foo
 */

echo \'bar\';',
                '<?php echo \'bar\';',
            ],
            [
                ['header' => 'x'],
                '<?php

/*
 * x
 */

echo \'a\';',
                '<?php

/*
 * y
 * z
 */

echo \'a\';',
            ],
            [
                ['header' => "a\na"],
                '<?php

/*
 * a
 * a
 */

echo \'x\';',
                '<?php


/*
 * b
 * c
 */


echo \'x\';',
            ],
            [
                [
                    'header' => 'foo',
                    'location' => 'after_open',
                    'separate' => 'bottom',
                    'comment_type' => 'PHPDoc',
                ],
                '<?php
/**
 * foo
 */

declare(strict_types=1);

namespace A;

echo 1;',
                '<?php

declare(strict_types=1);
/**
 * foo
 */

namespace A;

echo 1;',
            ],
            [
                [
                    'header' => 'foo',
                    'location' => 'after_open',
                    'separate' => 'bottom',
                    'comment_type' => 'PHPDoc',
                ],
                '<?php
/**
 * foo
 */

declare(strict_types=1);

namespace A;

echo 1;',
                '<?php

declare(strict_types=1);
/**
 * bar
 */

namespace A;

echo 1;',
            ],
            [
                [
                    'header' => 'Foo',
                    'separate' => 'none',
                ],
                '<?php

declare(strict_types=1);
/*
 * Foo
 */
namespace SebastianBergmann\Foo;

class Bar
{
}',
                '<?php
/*
 * Foo
 */

declare(strict_types=1);

namespace SebastianBergmann\Foo;

class Bar
{
}',
            ],
            [
                ['header' => 'tmp'],
                '<?php

/*
 * tmp
 */

/**
 * Foo class doc.
 */
class Foo {}',
                '<?php

/**
 * Foo class doc.
 */
class Foo {}',
            ],
            [
                ['header' => 'tmp'],
                '<?php

/*
 * tmp
 */

class Foo {}',
                '<?php

/*
 * Foo class doc.
 */
class Foo {}',
            ],
            [
                [
                    'header' => 'tmp',
                    'comment_type' => 'PHPDoc',
                ],
                '<?php

/**
 * tmp
 */

/**
 * Foo class doc.
 */
class Foo {}',
                '<?php

/**
 * Foo class doc.
 */
class Foo {}',
            ],
            [
                [
                    'header' => 'tmp',
                    'comment_type' => 'PHPDoc',
                ],
                '<?php

/**
 * tmp
 */

class Foo {}',
                '<?php

/**
 * tmp
 */
class Foo {}',
            ],
            [
                [
                    'header' => 'tmp',
                    'separate' => 'top',
                ],
                '<?php

/*
 * tmp
 */
class Foo {}',
                '<?php
/**
 * Foo class doc.
 */
class Foo {}',
            ],
        ];
    }

    public function testDefaultConfiguration()
    {
        $this->fixer->configure(['header' => 'a']);
        $this->doTest(
            '<?php

/*
 * a
 */

echo 1;',
            '<?php
echo 1;'
        );
    }

    /**
     * @group legacy
     * @expectedDeprecation Passing NULL to set default configuration is deprecated and will not be supported in 3.0, use an empty array instead.
     */
    public function testLegacyMisconfiguration()
    {
        $this->expectException(\PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException::class);
        $this->expectExceptionMessage('[header_comment] Missing required configuration: The required option "header" is missing.');

        $this->fixer->configure(null);
    }

    /**
     * @param null|array $configuration
     * @param string     $exceptionMessage
     *
     * @dataProvider provideMisconfigurationCases
     */
    public function testMisconfiguration($configuration, $exceptionMessage)
    {
        $this->expectException(\PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException::class);
        $this->expectExceptionMessage('[header_comment] '.$exceptionMessage);

        $this->configureFixerWithAliasedOptions($configuration);
    }

    public function provideMisconfigurationCases()
    {
        return [
            [[], 'Missing required configuration: The required option "header" is missing.'],
            [
                ['header' => 1],
                'Invalid configuration: The option "header" with value 1 is expected to be of type "string", but is of type "integer".',
            ],
            [
                [
                    'header' => '',
                    'comment_type' => 'foo',
                ],
                'Invalid configuration: The option "comment_type" with value "foo" is invalid. Accepted values are: "PHPDoc", "comment".',
            ],
            [
                [
                    'header' => '',
                    'comment_type' => new \stdClass(),
                ],
                'Invalid configuration: The option "comment_type" with value stdClass is invalid. Accepted values are: "PHPDoc", "comment".',
            ],
            [
                [
                    'header' => '',
                    'location' => new \stdClass(),
                ],
                'Invalid configuration: The option "location" with value stdClass is invalid. Accepted values are: "after_open", "after_declare_strict".',
            ],
            [
                [
                    'header' => '',
                    'separate' => new \stdClass(),
                ],
                'Invalid configuration: The option "separate" with value stdClass is invalid. Accepted values are: "both", "top", "bottom", "none".',
            ],
        ];
    }

    /**
     * @param string $expected
     * @param string $header
     * @param string $type
     *
     * @dataProvider provideHeaderGenerationCases
     */
    public function testHeaderGeneration($expected, $header, $type)
    {
        $this->configureFixerWithAliasedOptions([
            'header' => $header,
            'comment_type' => $type,
        ]);
        $this->doTest(
            '<?php

'.$expected.'

echo 1;',
            '<?php
echo 1;'
        );
    }

    public function provideHeaderGenerationCases()
    {
        return [
            [
                '/*
 * a
 */',
                'a',
                'comment',
            ],
            [
                '/**
 * a
 */',
                'a',
                'PHPDoc',
            ],
        ];
    }

    /**
     * @param string $expected
     *
     * @dataProvider provideDoNotTouchCases
     */
    public function testDoNotTouch($expected)
    {
        $this->fixer->configure([
            'header' => '',
        ]);

        $this->doTest($expected);
    }

    public function provideDoNotTouchCases()
    {
        return [
            ["<?php\nphpinfo();\n?>\n<?"],
            [" <?php\nphpinfo();\n"],
            ["<?php\nphpinfo();\n?><hr/>"],
            ["  <?php\n"],
            ['<?= 1?>'],
            ["<?= 1?><?php\n"],
            ["<?= 1?>\n<?php\n"],
        ];
    }

    public function testWithoutConfiguration()
    {
        $this->expectException(\PhpCsFixer\ConfigurationException\RequiredFixerConfigurationException::class);

        $this->doTest('<?php echo 1;');
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideMessyWhitespacesCases
     */
    public function testMessyWhitespaces(array $configuration, $expected, $input = null)
    {
        $this->fixer->setWhitespacesConfig(new WhitespacesFixerConfig("\t", "\r\n"));
        $this->configureFixerWithAliasedOptions($configuration);

        $this->doTest($expected, $input);
    }

    public function provideMessyWhitespacesCases()
    {
        return [
            [
                [
                    'header' => 'whitemess',
                    'location' => 'after_declare_strict',
                    'separate' => 'bottom',
                    'comment_type' => 'PHPDoc',
                ],
                "<?php\r\ndeclare(strict_types=1);\r\n/**\r\n * whitemess\r\n */\r\n\r\nnamespace A\\B;\r\n\r\necho 1;",
                "<?php\r\ndeclare(strict_types=1);\r\n\r\nnamespace A\\B;\r\n\r\necho 1;",
            ],
        ];
    }

    public function testConfigurationUpdatedWithWhitespsacesConfig()
    {
        $this->fixer->configure(['header' => 'Foo']);

        $this->doTest(
            "<?php\n\n/*\n * Foo\n */\n\necho 1;",
            "<?php\necho 1;"
        );

        $this->fixer->setWhitespacesConfig(new WhitespacesFixerConfig('    ', "\r\n"));

        $this->doTest(
            "<?php\r\n\r\n/*\r\n * Foo\r\n */\r\n\r\necho 1;",
            "<?php\r\necho 1;"
        );

        $this->fixer->configure(['header' => 'Bar']);

        $this->doTest(
            "<?php\r\n\r\n/*\r\n * Bar\r\n */\r\n\r\necho 1;",
            "<?php\r\necho 1;"
        );

        $this->fixer->setWhitespacesConfig(new WhitespacesFixerConfig('    ', "\n"));

        $this->doTest(
            "<?php\n\n/*\n * Bar\n */\n\necho 1;",
            "<?php\necho 1;"
        );
    }

    public function testInvalidHeaderConfiguration()
    {
        $this->expectException(InvalidFixerConfigurationException::class);
        $this->expectExceptionMessageRegExp('#^\[header_comment\] Cannot use \'\*/\' in header\.$#');

        $this->fixer->configure([
            'header' => '/** test */',
            'comment_type' => 'PHPDoc',
        ]);
    }
}
