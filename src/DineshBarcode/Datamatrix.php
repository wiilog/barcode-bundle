<?php

namespace SGK\BarcodeBundle\DineshBarcode;

// File name   : Datamatrix.php
// Author      : Dinesh Rabara - Tecnick.com LTD - Manor Coach House, Church Hill, Aldershot, Hants, GU12 4RQ, UK - www.tecnick.com - info@tecnick.com
// custom definitions
if (!defined('DATAMATRIXDEFS')) {
    /*
     * Indicate that definitions for this class are set
     */
    define('DATAMATRIXDEFS', true);

    // -----------------------------------------------------
} // end of custom definitions
// #*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#

/*
 * ASCII encoding: ASCII character 0 to 127 (1 byte per CW)
 */
define('ENC_ASCII', 0);

/*
 * C40 encoding: Upper-case alphanumeric (3/2 bytes per CW)
 */
define('ENC_C40', 1);

/*
 * TEXT encoding: Lower-case alphanumeric (3/2 bytes per CW)
 */
define('ENC_TXT', 2);

/*
 * X12 encoding: ANSI X12 (3/2 byte per CW)
 */
define('ENC_X12', 3);

/*
 * EDIFACT encoding: ASCII character 32 to 94 (4/3 bytes per CW)
 */
define('ENC_EDF', 4);

/*
 * BASE 256 encoding: ASCII character 0 to 255 (1 byte per CW)
 */
define('ENC_BASE256', 5);

/*
 * ASCII extended encoding: ASCII character 128 to 255 (1/2 byte per CW)
 */
define('ENC_ASCII_EXT', 6);

/*
 * ASCII number encoding: ASCII digits (2 bytes per CW)
 */
define('ENC_ASCII_NUM', 7);

class Datamatrix
{
    /**
     * Barcode array to be returned which is readable by Dinesh Rabara.
     *
     * @protected
     */
    protected $barcode_array = [];

    /**
     * Store last used encoding for data codewords.
     *
     * @protected
     */
    protected $last_enc = ENC_ASCII;

    /**
     * Table of Data Matrix ECC 200 Symbol Attributes:<ul>
     * <li>total matrix rows (including finder pattern)</li>
     * <li>total matrix cols (including finder pattern)</li>
     * <li>total matrix rows (without finder pattern)</li>
     * <li>total matrix cols (without finder pattern)</li>
     * <li>region data rows (with finder pattern)</li>
     * <li>region data col (with finder pattern)</li>
     * <li>region data rows (without finder pattern)</li>
     * <li>region data col (without finder pattern)</li>
     * <li>horizontal regions</li>
     * <li>vertical regions</li>
     * <li>regions</li>
     * <li>data codewords</li>
     * <li>error codewords</li>
     * <li>blocks</li>
     * <li>data codewords per block</li>
     * <li>error codewords per block</li>
     * </ul>.
     *
     * @protected
     */
    protected $symbattr = [
        // square form ---------------------------------------------------------------------------------------
        [0x00A, 0x00A, 0x008, 0x008, 0x00A, 0x00A, 0x008, 0x008, 0x001, 0x001, 0x001, 0x003, 0x005, 0x001, 0x003, 0x005],
        // 10x10
        [0x00C, 0x00C, 0x00A, 0x00A, 0x00C, 0x00C, 0x00A, 0x00A, 0x001, 0x001, 0x001, 0x005, 0x007, 0x001, 0x005, 0x007],
        // 12x12
        [0x00E, 0x00E, 0x00C, 0x00C, 0x00E, 0x00E, 0x00C, 0x00C, 0x001, 0x001, 0x001, 0x008, 0x00A, 0x001, 0x008, 0x00A],
        // 14x14
        [0x010, 0x010, 0x00E, 0x00E, 0x010, 0x010, 0x00E, 0x00E, 0x001, 0x001, 0x001, 0x00C, 0x00C, 0x001, 0x00C, 0x00C],
        // 16x16
        [0x012, 0x012, 0x010, 0x010, 0x012, 0x012, 0x010, 0x010, 0x001, 0x001, 0x001, 0x012, 0x00E, 0x001, 0x012, 0x00E],
        // 18x18
        [0x014, 0x014, 0x012, 0x012, 0x014, 0x014, 0x012, 0x012, 0x001, 0x001, 0x001, 0x016, 0x012, 0x001, 0x016, 0x012],
        // 20x20
        [0x016, 0x016, 0x014, 0x014, 0x016, 0x016, 0x014, 0x014, 0x001, 0x001, 0x001, 0x01E, 0x014, 0x001, 0x01E, 0x014],
        // 22x22
        [0x018, 0x018, 0x016, 0x016, 0x018, 0x018, 0x016, 0x016, 0x001, 0x001, 0x001, 0x024, 0x018, 0x001, 0x024, 0x018],
        // 24x24
        [0x01A, 0x01A, 0x018, 0x018, 0x01A, 0x01A, 0x018, 0x018, 0x001, 0x001, 0x001, 0x02C, 0x01C, 0x001, 0x02C, 0x01C],
        // 26x26
        [0x020, 0x020, 0x01C, 0x01C, 0x010, 0x010, 0x00E, 0x00E, 0x002, 0x002, 0x004, 0x03E, 0x024, 0x001, 0x03E, 0x024],
        // 32x32
        [0x024, 0x024, 0x020, 0x020, 0x012, 0x012, 0x010, 0x010, 0x002, 0x002, 0x004, 0x056, 0x02A, 0x001, 0x056, 0x02A],
        // 36x36
        [0x028, 0x028, 0x024, 0x024, 0x014, 0x014, 0x012, 0x012, 0x002, 0x002, 0x004, 0x072, 0x030, 0x001, 0x072, 0x030],
        // 40x40
        [0x02C, 0x02C, 0x028, 0x028, 0x016, 0x016, 0x014, 0x014, 0x002, 0x002, 0x004, 0x090, 0x038, 0x001, 0x090, 0x038],
        // 44x44
        [0x030, 0x030, 0x02C, 0x02C, 0x018, 0x018, 0x016, 0x016, 0x002, 0x002, 0x004, 0x0AE, 0x044, 0x001, 0x0AE, 0x044],
        // 48x48
        [0x034, 0x034, 0x030, 0x030, 0x01A, 0x01A, 0x018, 0x018, 0x002, 0x002, 0x004, 0x0CC, 0x054, 0x002, 0x066, 0x02A],
        // 52x52
        [0x040, 0x040, 0x038, 0x038, 0x010, 0x010, 0x00E, 0x00E, 0x004, 0x004, 0x010, 0x118, 0x070, 0x002, 0x08C, 0x038],
        // 64x64
        [0x048, 0x048, 0x040, 0x040, 0x012, 0x012, 0x010, 0x010, 0x004, 0x004, 0x010, 0x170, 0x090, 0x004, 0x05C, 0x024],
        // 72x72
        [0x050, 0x050, 0x048, 0x048, 0x014, 0x014, 0x012, 0x012, 0x004, 0x004, 0x010, 0x1C8, 0x0C0, 0x004, 0x072, 0x030],
        // 80x80
        [0x058, 0x058, 0x050, 0x050, 0x016, 0x016, 0x014, 0x014, 0x004, 0x004, 0x010, 0x240, 0x0E0, 0x004, 0x090, 0x038],
        // 88x88
        [0x060, 0x060, 0x058, 0x058, 0x018, 0x018, 0x016, 0x016, 0x004, 0x004, 0x010, 0x2B8, 0x110, 0x004, 0x0AE, 0x044],
        // 96x96
        [0x068, 0x068, 0x060, 0x060, 0x01A, 0x01A, 0x018, 0x018, 0x004, 0x004, 0x010, 0x330, 0x150, 0x006, 0x088, 0x038],
        // 104x104
        [0x078, 0x078, 0x06C, 0x06C, 0x014, 0x014, 0x012, 0x012, 0x006, 0x006, 0x024, 0x41A, 0x198, 0x006, 0x0AF, 0x044],
        // 120x120
        [0x084, 0x084, 0x078, 0x078, 0x016, 0x016, 0x014, 0x014, 0x006, 0x006, 0x024, 0x518, 0x1F0, 0x008, 0x0A3, 0x03E],
        // 132x132
        [0x090, 0x090, 0x084, 0x084, 0x018, 0x018, 0x016, 0x016, 0x006, 0x006, 0x024, 0x616, 0x26C, 0x00A, 0x09C, 0x03E],
        // 144x144
        // rectangular form (currently unused) ---------------------------------------------------------------------------
        [0x008, 0x012, 0x006, 0x010, 0x008, 0x012, 0x006, 0x010, 0x001, 0x001, 0x001, 0x005, 0x007, 0x001, 0x005, 0x007],
        // 8x18
        [0x008, 0x020, 0x006, 0x01C, 0x008, 0x010, 0x006, 0x00E, 0x001, 0x002, 0x002, 0x00A, 0x00B, 0x001, 0x00A, 0x00B],
        // 8x32
        [0x00C, 0x01A, 0x00A, 0x018, 0x00C, 0x01A, 0x00A, 0x018, 0x001, 0x001, 0x001, 0x010, 0x00E, 0x001, 0x010, 0x00E],
        // 12x26
        [0x00C, 0x024, 0x00A, 0x020, 0x00C, 0x012, 0x00A, 0x010, 0x001, 0x002, 0x002, 0x00C, 0x012, 0x001, 0x00C, 0x012],
        // 12x36
        [0x010, 0x024, 0x00E, 0x020, 0x010, 0x012, 0x00E, 0x010, 0x001, 0x002, 0x002, 0x020, 0x018, 0x001, 0x020, 0x018],
        // 16x36
        [0x010, 0x030, 0x00E, 0x02C, 0x010, 0x018, 0x00E, 0x016, 0x001, 0x002, 0x002, 0x031, 0x01C, 0x001, 0x031, 0x01C],
    ];

    /**
     * Map encodation modes whit character sets.
     *
     * @protected
     */
    protected $chset_id = [ENC_C40 => 'C40', ENC_TXT => 'TXT', ENC_X12 => 'X12'];

    /**
     * Basic set of charactes for each encodation mode.
     *
     * @protected
     */
    protected $chset = [
        'C40' => [
            // Basic set for C40 ----------------------------------------------------------------------------
            'S1' => 0x00,
            'S2' => 0x01,
            'S3' => 0x02,
            0x20 => 0x03,
            0x30 => 0x04,
            0x31 => 0x05,
            0x32 => 0x06,
            0x33 => 0x07,
            0x34 => 0x08,
            0x35 => 0x09,

            0x36 => 0x0A,
            0x37 => 0x0B,
            0x38 => 0x0C,
            0x39 => 0x0D,
            0x41 => 0x0E,
            0x42 => 0x0F,
            0x43 => 0x10,
            0x44 => 0x11,
            0x45 => 0x12,
            0x46 => 0x13,

            0x47 => 0x14,
            0x48 => 0x15,
            0x49 => 0x16,
            0x4A => 0x17,
            0x4B => 0x18,
            0x4C => 0x19,
            0x4D => 0x1A,
            0x4E => 0x1B,
            0x4F => 0x1C,
            0x50 => 0x1D,

            0x51 => 0x1E,
            0x52 => 0x1F,
            0x53 => 0x20,
            0x54 => 0x21,
            0x55 => 0x22,
            0x56 => 0x23,
            0x57 => 0x24,
            0x58 => 0x25,
            0x59 => 0x26,
            0x5A => 0x27,
        ],

        'TXT' => [
            // Basic set for TEXT ---------------------------------------------------------------------------
            'S1' => 0x00,
            'S2' => 0x01,
            'S3' => 0x02,
            0x20 => 0x03,
            0x30 => 0x04,
            0x31 => 0x05,
            0x32 => 0x06,
            0x33 => 0x07,
            0x34 => 0x08,
            0x35 => 0x09,

            0x36 => 0x0A,
            0x37 => 0x0B,
            0x38 => 0x0C,
            0x39 => 0x0D,
            0x61 => 0x0E,
            0x62 => 0x0F,
            0x63 => 0x10,
            0x64 => 0x11,
            0x65 => 0x12,
            0x66 => 0x13,

            0x67 => 0x14,
            0x68 => 0x15,
            0x69 => 0x16,
            0x6A => 0x17,
            0x6B => 0x18,
            0x6C => 0x19,
            0x6D => 0x1A,
            0x6E => 0x1B,
            0x6F => 0x1C,
            0x70 => 0x1D,

            0x71 => 0x1E,
            0x72 => 0x1F,
            0x73 => 0x20,
            0x74 => 0x21,
            0x75 => 0x22,
            0x76 => 0x23,
            0x77 => 0x24,
            0x78 => 0x25,
            0x79 => 0x26,
            0x7A => 0x27,
        ],

        'SH1' => [
            // Shift 1 set ----------------------------------------------------------------------------------
            0x00 => 0x00,
            0x01 => 0x01,
            0x02 => 0x02,
            0x03 => 0x03,
            0x04 => 0x04,
            0x05 => 0x05,
            0x06 => 0x06,
            0x07 => 0x07,
            0x08 => 0x08,
            0x09 => 0x09,

            0x0A => 0x0A,
            0x0B => 0x0B,
            0x0C => 0x0C,
            0x0D => 0x0D,
            0x0E => 0x0E,
            0x0F => 0x0F,
            0x10 => 0x10,
            0x11 => 0x11,
            0x12 => 0x12,
            0x13 => 0x13,

            0x14 => 0x14,
            0x15 => 0x15,
            0x16 => 0x16,
            0x17 => 0x17,
            0x18 => 0x18,
            0x19 => 0x19,
            0x1A => 0x1A,
            0x1B => 0x1B,
            0x1C => 0x1C,
            0x1D => 0x1D,

            0x1E => 0x1E,
            0x1F => 0x1F,
        ],

        'SH2' => [
            // Shift 2 set ----------------------------------------------------------------------------------
            0x21 => 0x00,
            0x22 => 0x01,
            0x23 => 0x02,
            0x24 => 0x03,
            0x25 => 0x04,
            0x26 => 0x05,
            0x27 => 0x06,
            0x28 => 0x07,
            0x29 => 0x08,
            0x2A => 0x09,

            0x2B => 0x0A,
            0x2C => 0x0B,
            0x2D => 0x0C,
            0x2E => 0x0D,
            0x2F => 0x0E,
            0x3A => 0x0F,
            0x3B => 0x10,
            0x3C => 0x11,
            0x3D => 0x12,
            0x3E => 0x13,

            0x3F => 0x14,
            0x40 => 0x15,
            0x5B => 0x16,
            0x5C => 0x17,
            0x5D => 0x18,
            0x5E => 0x19,
            0x5F => 0x1A,
            'F1' => 0x1B,
            'US' => 0x1E,
        ],

        'S3C' => [
            // Shift 3 set for C40 --------------------------------------------------------------------------
            0x60 => 0x00,
            0x61 => 0x01,
            0x62 => 0x02,
            0x63 => 0x03,
            0x64 => 0x04,
            0x65 => 0x05,
            0x66 => 0x06,
            0x67 => 0x07,
            0x68 => 0x08,
            0x69 => 0x09,

            0x6A => 0x0A,
            0x6B => 0x0B,
            0x6C => 0x0C,
            0x6D => 0x0D,
            0x6E => 0x0E,
            0x6F => 0x0F,
            0x70 => 0x10,
            0x71 => 0x11,
            0x72 => 0x12,
            0x73 => 0x13,

            0x74 => 0x14,
            0x75 => 0x15,
            0x76 => 0x16,
            0x77 => 0x17,
            0x78 => 0x18,
            0x79 => 0x19,
            0x7A => 0x1A,
            0x7B => 0x1B,
            0x7C => 0x1C,
            0x7D => 0x1D,

            0x7E => 0x1E,
            0x7F => 0x1F,
        ],
        'S3T' => [
            // Shift 3 set for TEXT -------------------------------------------------------------------------
            0x60 => 0x00,
            0x41 => 0x01,
            0x42 => 0x02,
            0x43 => 0x03,
            0x44 => 0x04,
            0x45 => 0x05,
            0x46 => 0x06,
            0x47 => 0x07,
            0x48 => 0x08,
            0x49 => 0x09,

            0x4A => 0x0A,
            0x4B => 0x0B,
            0x4C => 0x0C,
            0x4D => 0x0D,
            0x4E => 0x0E,
            0x4F => 0x0F,
            0x50 => 0x10,
            0x51 => 0x11,
            0x52 => 0x12,
            0x53 => 0x13,

            0x54 => 0x14,
            0x55 => 0x15,
            0x56 => 0x16,
            0x57 => 0x17,
            0x58 => 0x18,
            0x59 => 0x19,
            0x5A => 0x1A,
            0x7B => 0x1B,
            0x7C => 0x1C,
            0x7D => 0x1D,

            0x7E => 0x1E,
            0x7F => 0x1F,
        ],

        'X12' => [
            // Set for X12 ----------------------------------------------------------------------------------
            0x0D => 0x00,
            0x2A => 0x01,
            0x3E => 0x02,
            0x20 => 0x03,
            0x30 => 0x04,
            0x31 => 0x05,
            0x32 => 0x06,
            0x33 => 0x07,
            0x34 => 0x08,
            0x35 => 0x09,

            0x36 => 0x0A,
            0x37 => 0x0B,
            0x38 => 0x0C,
            0x39 => 0x0D,
            0x41 => 0x0E,
            0x42 => 0x0F,
            0x43 => 0x10,
            0x44 => 0x11,
            0x45 => 0x12,
            0x46 => 0x13,

            0x47 => 0x14,
            0x48 => 0x15,
            0x49 => 0x16,
            0x4A => 0x17,
            0x4B => 0x18,
            0x4C => 0x19,
            0x4D => 0x1A,
            0x4E => 0x1B,
            0x4F => 0x1C,
            0x50 => 0x1D,

            0x51 => 0x1E,
            0x52 => 0x1F,
            0x53 => 0x20,
            0x54 => 0x21,
            0x55 => 0x22,
            0x56 => 0x23,
            0x57 => 0x24,
            0x58 => 0x25,
            0x59 => 0x26,
            0x5A => 0x27,
        ],
    ];

    // -----------------------------------------------------------------------------

    /**
     * This is the class constructor.
     * Creates a datamatrix object.
     *
     * @param $code (string) Code to represent using Datamatrix.
     *
     * @public
     */
    public function __construct($code)
    {
        $params = [];
        $barcode_array = [];
        if (is_null($code) or ('\0' == $code) or ('' == $code)) {
            return false;
        }
        // get data codewords
        $cw = $this->getHighLevelEncoding($code);
        // number of data codewords
        $nd = count($cw);
        // check size
        if ($nd > 1558) {
            return false;
        }
        // get minimum required matrix size.
        foreach ($this->symbattr as $params) {
            if ($params[11] >= $nd) {
                break;
            }
        }
        if ($params[11] < $nd) {
            // too much data
            return false;
        } elseif ($params[11] > $nd) {
            // add padding
            if (ENC_EDF == $this->last_enc) {
                // switch to ASCII encoding
                $cw[] = 124;
                ++$nd;
            } elseif ((ENC_ASCII != $this->last_enc) and (ENC_BASE256 != $this->last_enc)) {
                // switch to ASCII encoding
                $cw[] = 254;
                ++$nd;
            }
            if ($params[11] > $nd) {
                // add first pad
                $cw[] = 129;
                ++$nd;
                // add remaining pads
                for ($i = $nd; $i <= $params[11]; ++$i) {
                    $cw[] = $this->get253StateCodeword(129, $i);
                }
            }
        }
        // add error correction codewords
        $cw = $this->getErrorCorrection($cw, $params[13], $params[14], $params[15]);
        // initialize empty arrays
        $grid = array_fill(0, $params[2] * $params[3], 0);
        // get placement map
        $places = $this->getPlacemetMap($params[2], $params[3]);
        // fill the grid with data
        $grid = [];
        $i = 0;
        // region data row max index
        $rdri = ($params[4] - 1);
        // region data column max index
        $rdci = ($params[5] - 1);
        // for each vertical region
        for ($vr = 0; $vr < $params[9]; ++$vr) {
            // for each row on region
            for ($r = 0; $r < $params[4]; ++$r) {
                // get row
                $row = (($vr * $params[4]) + $r);
                // for each horizontal region
                for ($hr = 0; $hr < $params[8]; ++$hr) {
                    // for each column on region
                    for ($c = 0; $c < $params[5]; ++$c) {
                        // get column
                        $col = (($hr * $params[5]) + $c);
                        // braw bits by case
                        if (0 == $r) {
                            // top finder pattern
                            if ($c % 2) {
                                $grid[$row][$col] = 0;
                            } else {
                                $grid[$row][$col] = 1;
                            }
                        } elseif ($r == $rdri) {
                            // bottom finder pattern
                            $grid[$row][$col] = 1;
                        } elseif (0 == $c) {
                            // left finder pattern
                            $grid[$row][$col] = 1;
                        } elseif ($c == $rdci) {
                            // right finder pattern
                            if ($r % 2) {
                                $grid[$row][$col] = 1;
                            } else {
                                $grid[$row][$col] = 0;
                            }
                        } else { // data bit
                            if ($places[$i] < 2) {
                                $grid[$row][$col] = $places[$i];
                            } else {
                                // codeword ID
                                $cw_id = (floor($places[$i] / 10) - 1);
                                // codeword BIT mask
                                $cw_bit = 2 ** (8 - ($places[$i] % 10));
                                $grid[$row][$col] = (($cw[$cw_id] & $cw_bit) == 0) ? 0 : 1;
                            }
                            ++$i;
                        }
                    }
                }
            }
        }
        $this->barcode_array['num_rows'] = $params[0];
        $this->barcode_array['num_cols'] = $params[1];
        $this->barcode_array['bcode'] = $grid;
    }

    /**
     * Returns a barcode array which is readable by Dinesh Rabara.
     *
     * @return array barcode array readable by Dinesh Rabara;
     *
     * @public
     */
    public function getBarcodeArray()
    {
        return $this->barcode_array;
    }

    /**
     * Product of two numbers in a Power-of-Two Galois Field.
     *
     * @param $a (int) first number to multiply.
     * @param $b (int) second number to multiply.
     * @param $log (array) Log table.
     * @param $alog (array) Anti-Log table.
     * @param $gf Number of Factors of the Reed-Solomon polynomial.
     *
     * @return int product
     *
     * @protected
     */
    protected function getGFProduct($a, $b, $log, $alog, $gf)
    {
        if ((0 == $a) or (0 == $b)) {
            return 0;
        }

        return $alog[($log[$a] + $log[$b]) % ($gf - 1)];
    }

    /**
     * Add error correction codewords to data codewords array (ANNEX E).
     *
     * @param $wd (array) Array of datacodewords.
     * @param $nb (int) Number of blocks.
     * @param $nd (int) Number of data codewords per block.
     * @param $nc (int) Number of correction codewords per block.
     * @param $gf (int) numner of fields on log/antilog table (power of 2).
     * @param $pp (int) The value of its prime modulus polynomial (301 for ECC200).
     *
     * @return array data codewords + error codewords
     *
     * @protected
     */
    protected function getErrorCorrection($wd, $nb, $nd, $nc, $gf = 256, $pp = 301)
    {
        $log = [];
        $alog = [];
        // generate the log ($log) and antilog ($alog) tables
        $log[0] = 0;
        $alog[0] = 1;
        for ($i = 1; $i < $gf; ++$i) {
            $alog[$i] = ($alog[$i - 1] * 2);
            if ($alog[$i] >= $gf) {
                $alog[$i] ^= $pp;
            }
            $log[$alog[$i]] = $i;
        }
        ksort($log);
        // generate the polynomial coefficients (c)
        $c = array_fill(0, $nc + 1, 0);
        $c[0] = 1;
        for ($i = 1; $i <= $nc; ++$i) {
            $c[$i] = $c[$i - 1];
            for ($j = ($i - 1); $j >= 1; --$j) {
                $c[$j] = $c[$j - 1] ^ $this->getGFProduct($c[$j], $alog[$i], $log, $alog, $gf);
            }
            $c[0] = $this->getGFProduct($c[0], $alog[$i], $log, $alog, $gf);
        }
        ksort($c);
        // total number of data codewords
        $num_wd = ($nb * $nd);
        // total number of error codewords
        $num_we = ($nb * $nc);
        // for each block
        for ($b = 0; $b < $nb; ++$b) {
            // create interleaved data block
            $block = [];
            for ($n = $b; $n < $num_wd; $n += $nb) {
                $block[] = $wd[$n];
            }
            // initialize error codewords
            $we = array_fill(0, $nc + 1, 0);
            // calculate error correction codewords for this block
            for ($i = 0; $i < $nd; ++$i) {
                $k = ($we[0] ^ $block[$i]);
                for ($j = 0; $j < $nc; ++$j) {
                    $we[$j] = ($we[$j + 1] ^ $this->getGFProduct($k, $c[$nc - $j - 1], $log, $alog, $gf));
                }
            }
            // add error codewords at the end of data codewords
            $j = 0;
            for ($i = $b; $i < $num_we; $i += $nb) {
                $wd[$num_wd + $i] = $we[$j];
                ++$j;
            }
        }
        // reorder codewords
        ksort($wd);

        return $wd;
    }

    /**
     * Return the 253-state codeword.
     *
     * @param $cwpad (int) Pad codeword.
     * @param $cwpos (int) Number of data codewords from the beginning of encoded data.
     *
     * @return pad codeword
     *
     * @protected
     */
    protected function get253StateCodeword($cwpad, $cwpos)
    {
        $pad = ($cwpad + (((149 * $cwpos) % 253) + 1));
        if ($pad > 254) {
            $pad -= 254;
        }

        return $pad;
    }

    /**
     * Return the 255-state codeword.
     *
     * @param $cwpad (int) Pad codeword.
     * @param $cwpos (int) Number of data codewords from the beginning of encoded data.
     *
     * @return pad codeword
     *
     * @protected
     */
    protected function get255StateCodeword($cwpad, $cwpos)
    {
        $pad = ($cwpad + (((149 * $cwpos) % 255) + 1));
        if ($pad > 255) {
            $pad -= 256;
        }

        return $pad;
    }

    /**
     * Returns true if the char belongs to the selected mode.
     *
     * @param $chr (int) Character (byte) to check.
     * @param $mode (int) Current encoding mode.
     *
     * @return bool true if the char is of the selected mode.
     *
     * @protected
     */
    protected function isCharMode($chr, $mode)
    {
        $status = false;
        switch ($mode) {
            case ENC_ASCII:  // ASCII character 0 to 127
                $status = (($chr >= 0) and ($chr <= 127));
                break;

            case ENC_C40:  // Upper-case alphanumeric
                $status = ((32 == $chr) or (($chr >= 48) and ($chr <= 57)) or (($chr >= 65) and ($chr <= 90)));
                break;

            case ENC_TXT:  // Lower-case alphanumeric
                $status = ((32 == $chr) or (($chr >= 48) and ($chr <= 57)) or (($chr >= 97) and ($chr <= 122)));
                break;

            case ENC_X12:  // ANSI X12
                $status = ((13 == $chr) or (42 == $chr) or (62 == $chr));
                break;

            case ENC_EDF:  // ASCII character 32 to 94
                $status = (($chr >= 32) and ($chr <= 94));
                break;

            case ENC_BASE256:  // Function character (FNC1, Structured Append, Reader Program, or Code Page)
                $status = ((232 == $chr) or (233 == $chr) or (234 == $chr) or (241 == $chr));
                break;

            case ENC_ASCII_EXT:  // ASCII character 128 to 255
                $status = (($chr >= 128) and ($chr <= 255));
                break;

            case ENC_ASCII_NUM:  // ASCII digits
                $status = (($chr >= 48) and ($chr <= 57));
                break;
        }

        return $status;
    }

    /**
     * The look-ahead test scans the data to be encoded to find the best mode (Annex P - steps from J to S).
     *
     * @param $data (string) data to encode
     * @param $pos (int) current position
     * @param $mode (int) current encoding mode
     *
     * @return int encoding mode
     *
     * @protected
     */
    protected function lookAheadTest($data, $pos, $mode)
    {
        $data_length = strlen($data);
        if ($pos >= $data_length) {
            return $mode;
        }
        $charscount = 0; // count processed chars
        // STEP J
        if (ENC_ASCII == $mode) {
            $numch = [0, 1, 1, 1, 1, 1.25];
        } else {
            $numch = [1, 2, 2, 2, 2, 2.25];
            $numch[$mode] = 0;
        }
        while (true) {
            // STEP K
            if (($pos + $charscount) == $data_length) {
                if ($numch[ENC_ASCII] <= ceil(min($numch[ENC_C40], $numch[ENC_TXT], $numch[ENC_X12], $numch[ENC_EDF], $numch[ENC_BASE256]))) {
                    return ENC_ASCII;
                }
                if ($numch[ENC_BASE256] < ceil(min($numch[ENC_ASCII], $numch[ENC_C40], $numch[ENC_TXT], $numch[ENC_X12], $numch[ENC_EDF]))) {
                    return ENC_BASE256;
                }
                if ($numch[ENC_EDF] < ceil(min($numch[ENC_ASCII], $numch[ENC_C40], $numch[ENC_TXT], $numch[ENC_X12], $numch[ENC_BASE256]))) {
                    return ENC_EDF;
                }
                if ($numch[ENC_TXT] < ceil(min($numch[ENC_ASCII], $numch[ENC_C40], $numch[ENC_X12], $numch[ENC_EDF], $numch[ENC_BASE256]))) {
                    return ENC_TXT;
                }
                if ($numch[ENC_X12] < ceil(min($numch[ENC_ASCII], $numch[ENC_C40], $numch[ENC_TXT], $numch[ENC_EDF], $numch[ENC_BASE256]))) {
                    return ENC_X12;
                }

                return ENC_C40;
            }
            // get char
            $chr = ord($data[$pos + $charscount]);
            ++$charscount;
            // STEP L
            if ($this->isCharMode($chr, ENC_ASCII_NUM)) {
                $numch[ENC_ASCII] += (1 / 2);
            } elseif ($this->isCharMode($chr, ENC_ASCII_EXT)) {
                $numch[ENC_ASCII] = ceil($numch[ENC_ASCII]);
                $numch[ENC_ASCII] += 2;
            } else {
                $numch[ENC_ASCII] = ceil($numch[ENC_ASCII]);
                ++$numch[ENC_ASCII];
            }
            // STEP M
            if ($this->isCharMode($chr, ENC_C40)) {
                $numch[ENC_C40] += (2 / 3);
            } elseif ($this->isCharMode($chr, ENC_ASCII_EXT)) {
                $numch[ENC_C40] += (8 / 3);
            } else {
                $numch[ENC_C40] += (4 / 3);
            }
            // STEP N
            if ($this->isCharMode($chr, ENC_TXT)) {
                $numch[ENC_TXT] += (2 / 3);
            } elseif ($this->isCharMode($chr, ENC_ASCII_EXT)) {
                $numch[ENC_TXT] += (8 / 3);
            } else {
                $numch[ENC_TXT] += (4 / 3);
            }
            // STEP O
            if ($this->isCharMode($chr, ENC_X12) or $this->isCharMode($chr, ENC_C40)) {
                $numch[ENC_X12] += (2 / 3);
            } elseif ($this->isCharMode($chr, ENC_ASCII_EXT)) {
                $numch[ENC_X12] += (13 / 3);
            } else {
                $numch[ENC_X12] += (10 / 3);
            }
            // STEP P
            if ($this->isCharMode($chr, ENC_EDF)) {
                $numch[ENC_EDF] += (3 / 4);
            } elseif ($this->isCharMode($chr, ENC_ASCII_EXT)) {
                $numch[ENC_EDF] += (17 / 4);
            } else {
                $numch[ENC_EDF] += (13 / 4);
            }
            // STEP Q
            if ($this->isCharMode($chr, ENC_BASE256)) {
                $numch[ENC_BASE256] += 4;
            } else {
                ++$numch[ENC_BASE256];
            }
            // STEP R
            if ($charscount >= 4) {
                if (($numch[ENC_ASCII] + 1) <= min($numch[ENC_C40], $numch[ENC_TXT], $numch[ENC_X12], $numch[ENC_EDF], $numch[ENC_BASE256])) {
                    return ENC_ASCII;
                }
                if ((($numch[ENC_BASE256] + 1) <= $numch[ENC_ASCII])
                        or (($numch[ENC_BASE256] + 1) < min($numch[ENC_C40], $numch[ENC_TXT], $numch[ENC_X12], $numch[ENC_EDF]))) {
                    return ENC_BASE256;
                }
                if (($numch[ENC_EDF] + 1) < min($numch[ENC_ASCII], $numch[ENC_C40], $numch[ENC_TXT], $numch[ENC_X12], $numch[ENC_BASE256])) {
                    return ENC_EDF;
                }
                if (($numch[ENC_TXT] + 1) < min($numch[ENC_ASCII], $numch[ENC_C40], $numch[ENC_X12], $numch[ENC_EDF], $numch[ENC_BASE256])) {
                    return ENC_TXT;
                }
                if (($numch[ENC_X12] + 1) < min($numch[ENC_ASCII], $numch[ENC_C40], $numch[ENC_TXT], $numch[ENC_EDF], $numch[ENC_BASE256])) {
                    return ENC_X12;
                }
                if (($numch[ENC_C40] + 1) < min($numch[ENC_ASCII], $numch[ENC_TXT], $numch[ENC_EDF], $numch[ENC_BASE256])) {
                    if ($numch[ENC_C40] < $numch[ENC_X12]) {
                        return ENC_C40;
                    }
                    if ($numch[ENC_C40] == $numch[ENC_X12]) {
                        $k = ($pos + $charscount + 1);
                        while ($k < $data_length) {
                            $tmpchr = ord($data[$k]);
                            if ($this->isCharMode($tmpchr, ENC_X12)) {
                                return ENC_X12;
                            } elseif (!($this->isCharMode($tmpchr, ENC_X12) or $this->isCharMode($tmpchr, ENC_C40))) {
                                break;
                            }
                            ++$k;
                        }

                        return ENC_C40;
                    }
                }
            }
        } // end of while
    }

    /**
     * Get the switching codeword to a new encoding mode (latch codeword).
     *
     * @param $mode (int) New encoding mode.
     *
     * @return (int) Switch codeword.
     *
     * @protected
     */
    protected function getSwitchEncodingCodeword($mode)
    {
        $cw = null;
        switch ($mode) {
            case ENC_ASCII:  // ASCII character 0 to 127
                $cw = 254;
                break;

            case ENC_C40:  // Upper-case alphanumeric
                $cw = 230;
                break;

            case ENC_TXT:  // Lower-case alphanumeric
                $cw = 239;
                break;

            case ENC_X12:  // ANSI X12
                $cw = 238;
                break;

            case ENC_EDF:  // ASCII character 32 to 94
                $cw = 240;
                break;

            case ENC_BASE256:  // Function character (FNC1, Structured Append, Reader Program, or Code Page)
                $cw = 231;
                break;
        }

        return $cw;
    }

    /**
     * Choose the minimum matrix size and return the max number of data codewords.
     *
     * @param $numcw (int) Number of current codewords.
     *
     * @return number of data codewords in matrix
     *
     * @protected
     */
    protected function getMaxDataCodewords($numcw)
    {
        foreach ($this->symbattr as $key => $matrix) {
            if ($matrix[11] >= $numcw) {
                return $matrix[11];
            }
        }

        return 0;
    }

    /**
     * Get high level encoding using the minimum symbol data characters for ECC 200.
     *
     * @param $data (string) data to encode
     *
     * @return array of codewords
     *
     * @protected
     */
    protected function getHighLevelEncoding($data)
    {
        // STEP A. Start in ASCII encodation.
        $enc = ENC_ASCII; // current encoding mode
        $pos = 0; // current position
        $cw = []; // array of codewords to be returned
        $cw_num = 0; // number of data codewords
        $data_lenght = strlen($data); // number of chars
        while ($pos < $data_lenght) {
            switch ($enc) {
                case ENC_ASCII:  // STEP B. While in ASCII encodation
                    if (($data_lenght > 1) and ($pos < ($data_lenght - 1)) and ($this->isCharMode(ord($data[$pos]), ENC_ASCII_NUM) and $this->isCharMode(ord($data[$pos + 1]), ENC_ASCII_NUM))) {
                        // 1. If the next data sequence is at least 2 consecutive digits, encode the next two digits as a double digit in ASCII mode.
                        $cw[] = (intval(substr($data, $pos, 2)) + 130);
                        ++$cw_num;
                        $pos += 2;
                    } else {
                        // 2. If the look-ahead test (starting at step J) indicates another mode, switch to that mode.
                        $newenc = $this->lookAheadTest($data, $pos, $enc);
                        if ($newenc != $enc) {
                            // switch to new encoding
                            $enc = $newenc;
                            $cw[] = $this->getSwitchEncodingCodeword($enc);
                            ++$cw_num;
                        } else {
                            // get new byte
                            $chr = ord($data[$pos]);
                            ++$pos;
                            if ($this->isCharMode($chr, ENC_ASCII_EXT)) {
                                // 3. If the next data character is extended ASCII (greater than 127) encode it in ASCII mode first using the Upper Shift (value 235) character.
                                $cw[] = 235;
                                $cw[] = ($chr - 127);
                                $cw_num += 2;
                            } else {
                                // 4. Otherwise process the next data character in ASCII encodation.
                                $cw[] = ($chr + 1);
                                ++$cw_num;
                            }
                        }
                    }
                    break;

                case ENC_C40:   // Upper-case alphanumeric
                case ENC_TXT:   // Lower-case alphanumeric
                case ENC_X12:  // ANSI X12
                    $temp_cw = [];
                    $p = 0;
                    $epos = $pos;
                    // get charset ID
                    $set_id = $this->chset_id[$enc];
                    // get basic charset for current encoding
                    $charset = $this->chset[$set_id];
                    do {
                        // 2. process the next character in C40 encodation.
                        $chr = ord($data[$epos]);
                        ++$epos;
                        // check for extended character
                        if ($chr & 0x80) {
                            if (ENC_X12 == $enc) {
                                return false;
                            }
                            $chr = ($chr & 0x7F);
                            $temp_cw[] = 1; // shift 2
                            $temp_cw[] = 30; // upper shift
                            $p += 2;
                        }
                        if (isset($charset[$chr])) {
                            $temp_cw[] = $charset[$chr];
                            ++$p;
                        } else {
                            if (isset($this->chset['SH1'][$chr])) {
                                $temp_cw[] = 0; // shift 1
                                $shiftset = $this->chset['SH1'];
                            } elseif (isset($chr, $this->chset['SH2'][$chr])) {
                                $temp_cw[] = 1; // shift 2
                                $shiftset = $this->chset['SH2'];
                            } elseif ((ENC_C40 == $enc) and isset($this->chset['S3C'][$chr])) {
                                $temp_cw[] = 2; // shift 3
                                $shiftset = $this->chset['S3C'];
                            } elseif ((ENC_TXT == $enc) and isset($this->chset['S3T'][$chr])) {
                                $temp_cw[] = 2; // shift 3
                                $shiftset = $this->chset['S3T'];
                            } else {
                                return false;
                            }
                            $temp_cw[] = $shiftset[$chr];
                            $p += 2;
                        }
                        if ($p >= 3) {
                            $c1 = array_shift($temp_cw);
                            $c2 = array_shift($temp_cw);
                            $c3 = array_shift($temp_cw);
                            $p -= 3;
                            $tmp = ((1600 * $c1) + (40 * $c2) + $c3 + 1);
                            $cw[] = ($tmp >> 8);
                            $cw[] = ($tmp % 256);
                            $cw_num += 2;
                            $pos = $epos;
                            // 1. If the C40 encoding is at the point of starting a new double symbol character and if the look-ahead test (starting at step J) indicates another mode, switch to that mode.
                            $newenc = $this->lookAheadTest($data, $pos, $enc);
                            if ($newenc != $enc) {
                                $enc = $newenc;
                                $cw[] = $this->getSwitchEncodingCodeword($enc);
                                ++$cw_num;
                                break;
                            }
                        }
                    } while (($p > 0) and ($epos < $data_lenght));
                    // process last data (if any)
                    if ($p > 0) {
                        // get remaining number of data symbols
                        $cwr = ($this->getMaxDataCodewords($cw_num + 2) - $cw_num);
                        if ((1 == $cwr) and (1 == $p)) {
                            // d. If one symbol character remains and one C40 value (data character) remains to be encoded
                            $c1 = array_shift($temp_cw);
                            --$p;
                            $cw[] = ($c1 + 1);
                            ++$cw_num;
                        } elseif ((2 == $cwr) and (1 == $p)) {
                            // c. If two symbol characters remain and only one C40 value (data character) remains to be encoded
                            $c1 = array_shift($temp_cw);
                            --$p;
                            $cw[] = 254;
                            $cw[] = ($c1 + 1);
                            $cw_num += 2;
                        } elseif ((2 == $cwr) and (2 == $p)) {
                            // b. If two symbol characters remain and two C40 values remain to be encoded
                            $c1 = array_shift($temp_cw);
                            $c2 = array_shift($temp_cw);
                            $p -= 2;
                            $tmp = ((1600 * $c1) + (40 * $c2) + 1);
                            $cw[] = ($tmp >> 8);
                            $cw[] = ($tmp % 256);
                            $cw_num += 2;
                        } else {
                            // switch to ASCII encoding
                            $enc = ENC_ASCII;
                            $cw[] = $this->getSwitchEncodingCodeword($enc);
                            ++$cw_num;
                        }
                    }
                    break;

                case ENC_EDF:  // F. While in EDIFACT (EDF) encodation
                    // initialize temporary array with 0 lenght
                    $temp_cw = [];
                    $epos = $pos;
                    $field_lenght = 0;
                    while ($epos < $data_lenght) {
                        // 2. process the next character in EDIFACT encodation.
                        $chr = ord($data[$epos]);
                        ++$epos;
                        $temp_cw[] = $chr;
                        ++$field_lenght;
                        if ((4 == $field_lenght) or ($epos == $data_lenght)) {
                            if ($field_lenght < 4) {
                                // set unlatch character
                                $temp_cw[] = 0x1F;
                                ++$field_lenght;
                                $enc = ENC_ASCII;
                                // fill empty characters
                                for ($i = $field_lenght; $i < 4; ++$i) {
                                    $temp_cw[] = 0;
                                }
                            }
                            // encodes four data characters in three codewords
                            $cw[] = (($temp_cw[0] & 0x3F) << 2) + (($temp_cw[1] & 0x30) >> 4);
                            $cw[] = (($temp_cw[1] & 0x0F) << 4) + (($temp_cw[2] & 0x3C) >> 2);
                            $cw[] = (($temp_cw[2] & 0x03) << 6) + ($temp_cw[3] & 0x3F);
                            $cw_num += 3;
                            $temp_cw = [];
                            $pos = $epos;
                            $field_lenght = 0;
                        }
                        // 1. If the EDIFACT encoding is at the point of starting a new triple symbol character and if the look-ahead test (starting at step J) indicates another mode, switch to that mode.
                        if (0 == $field_lenght) {
                            // get remaining number of data symbols
                            $cwr = ($this->getMaxDataCodewords($cw_num + 2) - $cw_num);
                            if ($cwr < 3) {
                                // return to ascii without unlatch
                                $enc = ENC_ASCII;
                                break; // exit from EDIFACT mode
                            }
                            $newenc = $this->lookAheadTest($data, $pos, $enc);
                            if ($newenc != $enc) {
                                // 1. If the look-ahead test (starting at step J) indicates another mode, switch to that mode.
                                $enc = $newenc;
                                $cw[] = $this->getSwitchEncodingCodeword($enc);
                                ++$cw_num;
                                break; // exit from EDIFACT mode
                            }
                        }
                    }
                    break;

                case ENC_BASE256:  // G. While in Base 256 (B256) encodation
                    // initialize temporary array with 0 lenght
                    $temp_cw = [];
                    $field_lenght = 0;
                    while (($pos < $data_lenght) and ($field_lenght <= 1555)) {
                        $newenc = $this->lookAheadTest($data, $pos, $enc);
                        if ($newenc != $enc) {
                            // 1. If the look-ahead test (starting at step J) indicates another mode, switch to that mode.
                            $enc = $newenc;
                            $cw[] = $this->getSwitchEncodingCodeword($enc);
                            ++$cw_num;
                            break; // exit from B256 mode
                        }
                        // 2. Otherwise, process the next character in Base 256 encodation.
                        $chr = ord($data[$pos]);
                        ++$pos;
                        $temp_cw[] = $chr;
                        ++$field_lenght;
                    }
                    // set field lenght
                    if ($field_lenght <= 249) {
                        $cw[] = $field_lenght;
                        ++$cw_num;
                    } else {
                        $cw[] = (floor($field_lenght / 250) + 249);
                        $cw[] = ($field_lenght % 250);
                        $cw_num += 2;
                    }
                    if (!empty($temp_cw)) {
                        // add B256 field
                        foreach ($temp_cw as $p => $cht) {
                            $cw[] = $this->get255StateCodeword($chr, $cw_num + $p);
                        }
                    }
                    break;
            } // end of switch enc
        } // end of while
        // set last used encoding
        $this->last_enc = $enc;

        return $cw;
    }

    /**
     * Places "chr+bit" with appropriate wrapping within array[].
     * (Annex F - ECC 200 symbol character placement).
     *
     * @param $marr (array) Array of symbols.
     * @param $nrow (int) Number of rows.
     * @param $ncol (int) Number of columns.
     * @param $row (int) Row number.
     * @param $col (int) Column number.
     * @param $chr (int) Char byte.
     * @param $bit (int) Bit.
     *
     * @return array
     *
     * @protected
     */
    protected function placeModule($marr, $nrow, $ncol, $row, $col, $chr, $bit)
    {
        if ($row < 0) {
            $row += $nrow;
            $col += (4 - (($nrow + 4) % 8));
        }
        if ($col < 0) {
            $col += $ncol;
            $row += (4 - (($ncol + 4) % 8));
        }
        $marr[($row * $ncol) + $col] = ((10 * $chr) + $bit);

        return $marr;
    }

    /**
     * Places the 8 bits of a utah-shaped symbol character.
     * (Annex F - ECC 200 symbol character placement).
     *
     * @param $marr (array) Array of symbols.
     * @param $nrow (int) Number of rows.
     * @param $ncol (int) Number of columns.
     * @param $row (int) Row number.
     * @param $col (int) Column number.
     * @param $chr (int) Char byte.
     *
     * @return array
     *
     * @protected
     */
    protected function placeUtah($marr, $nrow, $ncol, $row, $col, $chr)
    {
        $marr = $this->placeModule($marr, $nrow, $ncol, $row - 2, $col - 2, $chr, 1);
        $marr = $this->placeModule($marr, $nrow, $ncol, $row - 2, $col - 1, $chr, 2);
        $marr = $this->placeModule($marr, $nrow, $ncol, $row - 1, $col - 2, $chr, 3);
        $marr = $this->placeModule($marr, $nrow, $ncol, $row - 1, $col - 1, $chr, 4);
        $marr = $this->placeModule($marr, $nrow, $ncol, $row - 1, $col, $chr, 5);
        $marr = $this->placeModule($marr, $nrow, $ncol, $row, $col - 2, $chr, 6);
        $marr = $this->placeModule($marr, $nrow, $ncol, $row, $col - 1, $chr, 7);
        $marr = $this->placeModule($marr, $nrow, $ncol, $row, $col, $chr, 8);

        return $marr;
    }

    /**
     * Places the 8 bits of the first special corner case.
     * (Annex F - ECC 200 symbol character placement).
     *
     * @param $marr (array) Array of symbols.
     * @param $nrow (int) Number of rows.
     * @param $ncol (int) Number of columns.
     * @param $chr (int) Char byte.
     *
     * @return array
     *
     * @protected
     */
    protected function placeCornerA($marr, $nrow, $ncol, $chr)
    {
        $marr = $this->placeModule($marr, $nrow, $ncol, $nrow - 1, 0, $chr, 1);
        $marr = $this->placeModule($marr, $nrow, $ncol, $nrow - 1, 1, $chr, 2);
        $marr = $this->placeModule($marr, $nrow, $ncol, $nrow - 1, 2, $chr, 3);
        $marr = $this->placeModule($marr, $nrow, $ncol, 0, $ncol - 2, $chr, 4);
        $marr = $this->placeModule($marr, $nrow, $ncol, 0, $ncol - 1, $chr, 5);
        $marr = $this->placeModule($marr, $nrow, $ncol, 1, $ncol - 1, $chr, 6);
        $marr = $this->placeModule($marr, $nrow, $ncol, 2, $ncol - 1, $chr, 7);
        $marr = $this->placeModule($marr, $nrow, $ncol, 3, $ncol - 1, $chr, 8);

        return $marr;
    }

    /**
     * Places the 8 bits of the second special corner case.
     * (Annex F - ECC 200 symbol character placement).
     *
     * @param $marr (array) Array of symbols.
     * @param $nrow (int) Number of rows.
     * @param $ncol (int) Number of columns.
     * @param $chr (int) Char byte.
     *
     * @return array
     *
     * @protected
     */
    protected function placeCornerB($marr, $nrow, $ncol, $chr)
    {
        $marr = $this->placeModule($marr, $nrow, $ncol, $nrow - 3, 0, $chr, 1);
        $marr = $this->placeModule($marr, $nrow, $ncol, $nrow - 2, 0, $chr, 2);
        $marr = $this->placeModule($marr, $nrow, $ncol, $nrow - 1, 0, $chr, 3);
        $marr = $this->placeModule($marr, $nrow, $ncol, 0, $ncol - 4, $chr, 4);
        $marr = $this->placeModule($marr, $nrow, $ncol, 0, $ncol - 3, $chr, 5);
        $marr = $this->placeModule($marr, $nrow, $ncol, 0, $ncol - 2, $chr, 6);
        $marr = $this->placeModule($marr, $nrow, $ncol, 0, $ncol - 1, $chr, 7);
        $marr = $this->placeModule($marr, $nrow, $ncol, 1, $ncol - 1, $chr, 8);

        return $marr;
    }

    /**
     * Places the 8 bits of the third special corner case.
     * (Annex F - ECC 200 symbol character placement).
     *
     * @param $marr (array) Array of symbols.
     * @param $nrow (int) Number of rows.
     * @param $ncol (int) Number of columns.
     * @param $chr (int) Char byte.
     *
     * @return array
     *
     * @protected
     */
    protected function placeCornerC($marr, $nrow, $ncol, $chr)
    {
        $marr = $this->placeModule($marr, $nrow, $ncol, $nrow - 3, 0, $chr, 1);
        $marr = $this->placeModule($marr, $nrow, $ncol, $nrow - 2, 0, $chr, 2);
        $marr = $this->placeModule($marr, $nrow, $ncol, $nrow - 1, 0, $chr, 3);
        $marr = $this->placeModule($marr, $nrow, $ncol, 0, $ncol - 2, $chr, 4);
        $marr = $this->placeModule($marr, $nrow, $ncol, 0, $ncol - 1, $chr, 5);
        $marr = $this->placeModule($marr, $nrow, $ncol, 1, $ncol - 1, $chr, 6);
        $marr = $this->placeModule($marr, $nrow, $ncol, 2, $ncol - 1, $chr, 7);
        $marr = $this->placeModule($marr, $nrow, $ncol, 3, $ncol - 1, $chr, 8);

        return $marr;
    }

    /**
     * Places the 8 bits of the fourth special corner case.
     * (Annex F - ECC 200 symbol character placement).
     *
     * @param $marr (array) Array of symbols.
     * @param $nrow (int) Number of rows.
     * @param $ncol (int) Number of columns.
     * @param $chr (int) Char byte.
     *
     * @return array
     *
     * @protected
     */
    protected function placeCornerD($marr, $nrow, $ncol, $chr)
    {
        $marr = $this->placeModule($marr, $nrow, $ncol, $nrow - 1, 0, $chr, 1);
        $marr = $this->placeModule($marr, $nrow, $ncol, $nrow - 1, $ncol - 1, $chr, 2);
        $marr = $this->placeModule($marr, $nrow, $ncol, 0, $ncol - 3, $chr, 3);
        $marr = $this->placeModule($marr, $nrow, $ncol, 0, $ncol - 2, $chr, 4);
        $marr = $this->placeModule($marr, $nrow, $ncol, 0, $ncol - 1, $chr, 5);
        $marr = $this->placeModule($marr, $nrow, $ncol, 1, $ncol - 3, $chr, 6);
        $marr = $this->placeModule($marr, $nrow, $ncol, 1, $ncol - 2, $chr, 7);
        $marr = $this->placeModule($marr, $nrow, $ncol, 1, $ncol - 1, $chr, 8);

        return $marr;
    }

    /**
     * Build a placement map.
     * (Annex F - ECC 200 symbol character placement).
     *
     * @param $nrow (int) Number of rows.
     * @param $ncol (int) Number of columns.
     *
     * @return array
     *
     * @protected
     */
    protected function getPlacemetMap($nrow, $ncol)
    {
        // initialize array with zeros
        $marr = array_fill(0, $nrow * $ncol, 0);
        // set starting values
        $chr = 1;
        $row = 4;
        $col = 0;
        do {
            // repeatedly first check for one of the special corner cases, then
            if (($row == $nrow) and (0 == $col)) {
                $marr = $this->placeCornerA($marr, $nrow, $ncol, $chr);
                ++$chr;
            }
            if (($row == ($nrow - 2)) and (0 == $col) and ($ncol % 4)) {
                $marr = $this->placeCornerB($marr, $nrow, $ncol, $chr);
                ++$chr;
            }
            if (($row == ($nrow - 2)) and (0 == $col) and (($ncol % 8) == 4)) {
                $marr = $this->placeCornerC($marr, $nrow, $ncol, $chr);
                ++$chr;
            }
            if (($row == ($nrow + 4)) and (2 == $col) and (!($ncol % 8))) {
                $marr = $this->placeCornerD($marr, $nrow, $ncol, $chr);
                ++$chr;
            }
            // sweep upward diagonally, inserting successive characters,
            do {
                if (($row < $nrow) and ($col >= 0) and (!$marr[($row * $ncol) + $col])) {
                    $marr = $this->placeUtah($marr, $nrow, $ncol, $row, $col, $chr);
                    ++$chr;
                }
                $row -= 2;
                $col += 2;
            } while (($row >= 0) and ($col < $ncol));
            ++$row;
            $col += 3;
            // & then sweep downward diagonally, inserting successive characters,...
            do {
                if (($row >= 0) and ($col < $ncol) and (!$marr[($row * $ncol) + $col])) {
                    $marr = $this->placeUtah($marr, $nrow, $ncol, $row, $col, $chr);
                    ++$chr;
                }
                $row += 2;
                $col -= 2;
            } while (($row < $nrow) and ($col >= 0));
            $row += 3;
            ++$col;
            // ... until the entire array is scanned
        } while (($row < $nrow) or ($col < $ncol));
        // lastly, if the lower righthand corner is untouched, fill in fixed pattern
        if (!$marr[($nrow * $ncol) - 1]) {
            $marr[($nrow * $ncol) - 1] = 1;
            $marr[($nrow * $ncol) - $ncol - 2] = 1;
        }

        return $marr;
    }
}

// end DataMatrix class
// ============================================================+
// END OF FILE
// ============================================================+
