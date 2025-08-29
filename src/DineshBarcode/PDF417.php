<?php

namespace SGK\BarcodeBundle\DineshBarcode;

// File name   : PDF417.php
// Author      : Dinesh Rabara

/*
 * @file
 * PDF417 (ISO/IEC 15438:2006) is a 2-dimensional stacked bar code
 * (requires PHP bcmath extension)
 * @author Dinesh Rabara
 */
// definitions
if (!defined('PDF417DEFS')) {
    /*
     * Indicate that definitions for this class are set
     */
    define('PDF417DEFS', true);

    // -----------------------------------------------------

    /*
     * Row height respect X dimension of single module
     */
    define('ROWHEIGHT', 4);

    /*
     * Horizontal quiet zone in modules
     */
    define('QUIETH', 2);

    /*
     * Vertical quiet zone in modules
     */
    define('QUIETV', 2);
} // end of definitions
// #*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#

/**
 * @class PDF417
 *
 * @author Dinesh Rabara
 */
class PDF417
{
    /**
     * Barcode array to be returned which is readable by Dinesh Rabara.
     *
     * @protected
     */
    protected $barcode_array = [];

    /**
     * Start pattern.
     *
     * @protected
     */
    protected $start_pattern = '11111111010101000';

    /**
     * Stop pattern.
     *
     * @protected
     */
    protected $stop_pattern = '111111101000101001';

    /**
     * Array of text Compaction Sub-Modes (values 0xFB - 0xFF are used for submode changers).
     *
     * @protected
     */
    protected $textsubmodes = [
        [0x41, 0x42, 0x43, 0x44, 0x45, 0x46, 0x47, 0x48, 0x49, 0x4A, 0x4B, 0x4C, 0x4D, 0x4E, 0x4F, 0x50, 0x51, 0x52, 0x53, 0x54, 0x55, 0x56, 0x57, 0x58, 0x59, 0x5A, 0x20, 0xFD, 0xFE, 0xFF],
        // Alpha
        [0x61, 0x62, 0x63, 0x64, 0x65, 0x66, 0x67, 0x68, 0x69, 0x6A, 0x6B, 0x6C, 0x6D, 0x6E, 0x6F, 0x70, 0x71, 0x72, 0x73, 0x74, 0x75, 0x76, 0x77, 0x78, 0x79, 0x7A, 0x20, 0xFD, 0xFE, 0xFF],
        // Lower
        [0x30, 0x31, 0x32, 0x33, 0x34, 0x35, 0x36, 0x37, 0x38, 0x39, 0x26, 0x0D, 0x09, 0x2C, 0x3A, 0x23, 0x2D, 0x2E, 0x24, 0x2F, 0x2B, 0x25, 0x2A, 0x3D, 0x5E, 0xFB, 0x20, 0xFD, 0xFE, 0xFF],
        // Mixed
        [0x3B, 0x3C, 0x3E, 0x40, 0x5B, 0x5C, 0x5D, 0x5F, 0x60, 0x7E, 0x21, 0x0D, 0x09, 0x2C, 0x3A, 0x0A, 0x2D, 0x2E, 0x24, 0x2F, 0x22, 0x7C, 0x2A, 0x28, 0x29, 0x3F, 0x7B, 0x7D, 0x27, 0xFF],
    ];

    /**
     * Array of switching codes for Text Compaction Sub-Modes.
     *
     * @protected
     */
    protected $textlatch = [
        '01' => [27],
        '02' => [28],
        '03' => [28, 25],

        '10' => [28, 28],
        '12' => [28],
        '13' => [28, 25],

        '20' => [28],
        '21' => [27],
        '23' => [25],

        '30' => [29],
        '31' => [29, 27],
        '32' => [29, 28],
    ];

    /**
     * Clusters of codewords (0, 3, 6)<br/>
     * Values are hex equivalents of binary representation of bars (1 = bar, 0 = space).<br/>
     * The codewords numbered from 900 to 928 have special meaning, some enable to switch between modes in order to optimise the code:<ul>
     * <li>900 : Switch to "Text" mode</li>
     * <li>901 : Switch to "Byte" mode</li>
     * <li>902 : Switch to "Numeric" mode</li>
     * <li>903 - 912 : Reserved</li>
     * <li>913 : Switch to "Octet" only for the next codeword</li>
     * <li>914 - 920 : Reserved</li>
     * <li>921 : Initialization</li>
     * <li>922 : Terminator codeword for Macro PDF control block</li>
     * <li>923 : Sequence tag to identify the beginning of optional fields in the Macro PDF control block</li>
     * <li>924 : Switch to "Byte" mode (If the total number of byte is multiple of 6)</li>
     * <li>925 : Identifier for a user defined Extended Channel Interpretation (ECI)</li>
     * <li>926 : Identifier for a general purpose ECI format</li>
     * <li>927 : Identifier for an ECI of a character set or code page</li>
     * <li>928 : Macro marker codeword to indicate the beginning of a Macro PDF Control Block</li>
     * </ul>.
     *
     * @protected
     */
    protected $clusters = [
        [
            // cluster 0 -----------------------------------------------------------------------
            0x1D5C0,
            0x1EAF0,
            0x1F57C,
            0x1D4E0,
            0x1EA78,
            0x1F53E,
            0x1A8C0,
            0x1D470,
            0x1A860,
            0x15040,
            //  10
            0x1A830,
            0x15020,
            0x1ADC0,
            0x1D6F0,
            0x1EB7C,
            0x1ACE0,
            0x1D678,
            0x1EB3E,
            0x158C0,
            0x1AC70,
            //  20
            0x15860,
            0x15DC0,
            0x1AEF0,
            0x1D77C,
            0x15CE0,
            0x1AE78,
            0x1D73E,
            0x15C70,
            0x1AE3C,
            0x15EF0,
            //  30
            0x1AF7C,
            0x15E78,
            0x1AF3E,
            0x15F7C,
            0x1F5FA,
            0x1D2E0,
            0x1E978,
            0x1F4BE,
            0x1A4C0,
            0x1D270,
            //  40
            0x1E93C,
            0x1A460,
            0x1D238,
            0x14840,
            0x1A430,
            0x1D21C,
            0x14820,
            0x1A418,
            0x14810,
            0x1A6E0,
            //  50
            0x1D378,
            0x1E9BE,
            0x14CC0,
            0x1A670,
            0x1D33C,
            0x14C60,
            0x1A638,
            0x1D31E,
            0x14C30,
            0x1A61C,
            //  60
            0x14EE0,
            0x1A778,
            0x1D3BE,
            0x14E70,
            0x1A73C,
            0x14E38,
            0x1A71E,
            0x14F78,
            0x1A7BE,
            0x14F3C,
            //  70
            0x14F1E,
            0x1A2C0,
            0x1D170,
            0x1E8BC,
            0x1A260,
            0x1D138,
            0x1E89E,
            0x14440,
            0x1A230,
            0x1D11C,
            //  80
            0x14420,
            0x1A218,
            0x14410,
            0x14408,
            0x146C0,
            0x1A370,
            0x1D1BC,
            0x14660,
            0x1A338,
            0x1D19E,
            //  90
            0x14630,
            0x1A31C,
            0x14618,
            0x1460C,
            0x14770,
            0x1A3BC,
            0x14738,
            0x1A39E,
            0x1471C,
            0x147BC,
            // 100
            0x1A160,
            0x1D0B8,
            0x1E85E,
            0x14240,
            0x1A130,
            0x1D09C,
            0x14220,
            0x1A118,
            0x1D08E,
            0x14210,
            // 110
            0x1A10C,
            0x14208,
            0x1A106,
            0x14360,
            0x1A1B8,
            0x1D0DE,
            0x14330,
            0x1A19C,
            0x14318,
            0x1A18E,
            // 120
            0x1430C,
            0x14306,
            0x1A1DE,
            0x1438E,
            0x14140,
            0x1A0B0,
            0x1D05C,
            0x14120,
            0x1A098,
            0x1D04E,
            // 130
            0x14110,
            0x1A08C,
            0x14108,
            0x1A086,
            0x14104,
            0x141B0,
            0x14198,
            0x1418C,
            0x140A0,
            0x1D02E,
            // 140
            0x1A04C,
            0x1A046,
            0x14082,
            0x1CAE0,
            0x1E578,
            0x1F2BE,
            0x194C0,
            0x1CA70,
            0x1E53C,
            0x19460,
            // 150
            0x1CA38,
            0x1E51E,
            0x12840,
            0x19430,
            0x12820,
            0x196E0,
            0x1CB78,
            0x1E5BE,
            0x12CC0,
            0x19670,
            // 160
            0x1CB3C,
            0x12C60,
            0x19638,
            0x12C30,
            0x12C18,
            0x12EE0,
            0x19778,
            0x1CBBE,
            0x12E70,
            0x1973C,
            // 170
            0x12E38,
            0x12E1C,
            0x12F78,
            0x197BE,
            0x12F3C,
            0x12FBE,
            0x1DAC0,
            0x1ED70,
            0x1F6BC,
            0x1DA60,
            // 180
            0x1ED38,
            0x1F69E,
            0x1B440,
            0x1DA30,
            0x1ED1C,
            0x1B420,
            0x1DA18,
            0x1ED0E,
            0x1B410,
            0x1DA0C,
            // 190
            0x192C0,
            0x1C970,
            0x1E4BC,
            0x1B6C0,
            0x19260,
            0x1C938,
            0x1E49E,
            0x1B660,
            0x1DB38,
            0x1ED9E,
            // 200
            0x16C40,
            0x12420,
            0x19218,
            0x1C90E,
            0x16C20,
            0x1B618,
            0x16C10,
            0x126C0,
            0x19370,
            0x1C9BC,
            // 210
            0x16EC0,
            0x12660,
            0x19338,
            0x1C99E,
            0x16E60,
            0x1B738,
            0x1DB9E,
            0x16E30,
            0x12618,
            0x16E18,
            // 220
            0x12770,
            0x193BC,
            0x16F70,
            0x12738,
            0x1939E,
            0x16F38,
            0x1B79E,
            0x16F1C,
            0x127BC,
            0x16FBC,
            // 230
            0x1279E,
            0x16F9E,
            0x1D960,
            0x1ECB8,
            0x1F65E,
            0x1B240,
            0x1D930,
            0x1EC9C,
            0x1B220,
            0x1D918,
            // 240
            0x1EC8E,
            0x1B210,
            0x1D90C,
            0x1B208,
            0x1B204,
            0x19160,
            0x1C8B8,
            0x1E45E,
            0x1B360,
            0x19130,
            // 250
            0x1C89C,
            0x16640,
            0x12220,
            0x1D99C,
            0x1C88E,
            0x16620,
            0x12210,
            0x1910C,
            0x16610,
            0x1B30C,
            // 260
            0x19106,
            0x12204,
            0x12360,
            0x191B8,
            0x1C8DE,
            0x16760,
            0x12330,
            0x1919C,
            0x16730,
            0x1B39C,
            // 270
            0x1918E,
            0x16718,
            0x1230C,
            0x12306,
            0x123B8,
            0x191DE,
            0x167B8,
            0x1239C,
            0x1679C,
            0x1238E,
            // 280
            0x1678E,
            0x167DE,
            0x1B140,
            0x1D8B0,
            0x1EC5C,
            0x1B120,
            0x1D898,
            0x1EC4E,
            0x1B110,
            0x1D88C,
            // 290
            0x1B108,
            0x1D886,
            0x1B104,
            0x1B102,
            0x12140,
            0x190B0,
            0x1C85C,
            0x16340,
            0x12120,
            0x19098,
            // 300
            0x1C84E,
            0x16320,
            0x1B198,
            0x1D8CE,
            0x16310,
            0x12108,
            0x19086,
            0x16308,
            0x1B186,
            0x16304,
            // 310
            0x121B0,
            0x190DC,
            0x163B0,
            0x12198,
            0x190CE,
            0x16398,
            0x1B1CE,
            0x1638C,
            0x12186,
            0x16386,
            // 320
            0x163DC,
            0x163CE,
            0x1B0A0,
            0x1D858,
            0x1EC2E,
            0x1B090,
            0x1D84C,
            0x1B088,
            0x1D846,
            0x1B084,
            // 330
            0x1B082,
            0x120A0,
            0x19058,
            0x1C82E,
            0x161A0,
            0x12090,
            0x1904C,
            0x16190,
            0x1B0CC,
            0x19046,
            // 340
            0x16188,
            0x12084,
            0x16184,
            0x12082,
            0x120D8,
            0x161D8,
            0x161CC,
            0x161C6,
            0x1D82C,
            0x1D826,
            // 350
            0x1B042,
            0x1902C,
            0x12048,
            0x160C8,
            0x160C4,
            0x160C2,
            0x18AC0,
            0x1C570,
            0x1E2BC,
            0x18A60,
            // 360
            0x1C538,
            0x11440,
            0x18A30,
            0x1C51C,
            0x11420,
            0x18A18,
            0x11410,
            0x11408,
            0x116C0,
            0x18B70,
            // 370
            0x1C5BC,
            0x11660,
            0x18B38,
            0x1C59E,
            0x11630,
            0x18B1C,
            0x11618,
            0x1160C,
            0x11770,
            0x18BBC,
            // 380
            0x11738,
            0x18B9E,
            0x1171C,
            0x117BC,
            0x1179E,
            0x1CD60,
            0x1E6B8,
            0x1F35E,
            0x19A40,
            0x1CD30,
            // 390
            0x1E69C,
            0x19A20,
            0x1CD18,
            0x1E68E,
            0x19A10,
            0x1CD0C,
            0x19A08,
            0x1CD06,
            0x18960,
            0x1C4B8,
            // 400
            0x1E25E,
            0x19B60,
            0x18930,
            0x1C49C,
            0x13640,
            0x11220,
            0x1CD9C,
            0x1C48E,
            0x13620,
            0x19B18,
            // 410
            0x1890C,
            0x13610,
            0x11208,
            0x13608,
            0x11360,
            0x189B8,
            0x1C4DE,
            0x13760,
            0x11330,
            0x1CDDE,
            // 420
            0x13730,
            0x19B9C,
            0x1898E,
            0x13718,
            0x1130C,
            0x1370C,
            0x113B8,
            0x189DE,
            0x137B8,
            0x1139C,
            // 430
            0x1379C,
            0x1138E,
            0x113DE,
            0x137DE,
            0x1DD40,
            0x1EEB0,
            0x1F75C,
            0x1DD20,
            0x1EE98,
            0x1F74E,
            // 440
            0x1DD10,
            0x1EE8C,
            0x1DD08,
            0x1EE86,
            0x1DD04,
            0x19940,
            0x1CCB0,
            0x1E65C,
            0x1BB40,
            0x19920,
            // 450
            0x1EEDC,
            0x1E64E,
            0x1BB20,
            0x1DD98,
            0x1EECE,
            0x1BB10,
            0x19908,
            0x1CC86,
            0x1BB08,
            0x1DD86,
            // 460
            0x19902,
            0x11140,
            0x188B0,
            0x1C45C,
            0x13340,
            0x11120,
            0x18898,
            0x1C44E,
            0x17740,
            0x13320,
            // 470
            0x19998,
            0x1CCCE,
            0x17720,
            0x1BB98,
            0x1DDCE,
            0x18886,
            0x17710,
            0x13308,
            0x19986,
            0x17708,
            // 480
            0x11102,
            0x111B0,
            0x188DC,
            0x133B0,
            0x11198,
            0x188CE,
            0x177B0,
            0x13398,
            0x199CE,
            0x17798,
            // 490
            0x1BBCE,
            0x11186,
            0x13386,
            0x111DC,
            0x133DC,
            0x111CE,
            0x177DC,
            0x133CE,
            0x1DCA0,
            0x1EE58,
            // 500
            0x1F72E,
            0x1DC90,
            0x1EE4C,
            0x1DC88,
            0x1EE46,
            0x1DC84,
            0x1DC82,
            0x198A0,
            0x1CC58,
            0x1E62E,
            // 510
            0x1B9A0,
            0x19890,
            0x1EE6E,
            0x1B990,
            0x1DCCC,
            0x1CC46,
            0x1B988,
            0x19884,
            0x1B984,
            0x19882,
            // 520
            0x1B982,
            0x110A0,
            0x18858,
            0x1C42E,
            0x131A0,
            0x11090,
            0x1884C,
            0x173A0,
            0x13190,
            0x198CC,
            // 530
            0x18846,
            0x17390,
            0x1B9CC,
            0x11084,
            0x17388,
            0x13184,
            0x11082,
            0x13182,
            0x110D8,
            0x1886E,
            // 540
            0x131D8,
            0x110CC,
            0x173D8,
            0x131CC,
            0x110C6,
            0x173CC,
            0x131C6,
            0x110EE,
            0x173EE,
            0x1DC50,
            // 550
            0x1EE2C,
            0x1DC48,
            0x1EE26,
            0x1DC44,
            0x1DC42,
            0x19850,
            0x1CC2C,
            0x1B8D0,
            0x19848,
            0x1CC26,
            // 560
            0x1B8C8,
            0x1DC66,
            0x1B8C4,
            0x19842,
            0x1B8C2,
            0x11050,
            0x1882C,
            0x130D0,
            0x11048,
            0x18826,
            // 570
            0x171D0,
            0x130C8,
            0x19866,
            0x171C8,
            0x1B8E6,
            0x11042,
            0x171C4,
            0x130C2,
            0x171C2,
            0x130EC,
            // 580
            0x171EC,
            0x171E6,
            0x1EE16,
            0x1DC22,
            0x1CC16,
            0x19824,
            0x19822,
            0x11028,
            0x13068,
            0x170E8,
            // 590
            0x11022,
            0x13062,
            0x18560,
            0x10A40,
            0x18530,
            0x10A20,
            0x18518,
            0x1C28E,
            0x10A10,
            0x1850C,
            // 600
            0x10A08,
            0x18506,
            0x10B60,
            0x185B8,
            0x1C2DE,
            0x10B30,
            0x1859C,
            0x10B18,
            0x1858E,
            0x10B0C,
            // 610
            0x10B06,
            0x10BB8,
            0x185DE,
            0x10B9C,
            0x10B8E,
            0x10BDE,
            0x18D40,
            0x1C6B0,
            0x1E35C,
            0x18D20,
            // 620
            0x1C698,
            0x18D10,
            0x1C68C,
            0x18D08,
            0x1C686,
            0x18D04,
            0x10940,
            0x184B0,
            0x1C25C,
            0x11B40,
            // 630
            0x10920,
            0x1C6DC,
            0x1C24E,
            0x11B20,
            0x18D98,
            0x1C6CE,
            0x11B10,
            0x10908,
            0x18486,
            0x11B08,
            // 640
            0x18D86,
            0x10902,
            0x109B0,
            0x184DC,
            0x11BB0,
            0x10998,
            0x184CE,
            0x11B98,
            0x18DCE,
            0x11B8C,
            // 650
            0x10986,
            0x109DC,
            0x11BDC,
            0x109CE,
            0x11BCE,
            0x1CEA0,
            0x1E758,
            0x1F3AE,
            0x1CE90,
            0x1E74C,
            // 660
            0x1CE88,
            0x1E746,
            0x1CE84,
            0x1CE82,
            0x18CA0,
            0x1C658,
            0x19DA0,
            0x18C90,
            0x1C64C,
            0x19D90,
            // 670
            0x1CECC,
            0x1C646,
            0x19D88,
            0x18C84,
            0x19D84,
            0x18C82,
            0x19D82,
            0x108A0,
            0x18458,
            0x119A0,
            // 680
            0x10890,
            0x1C66E,
            0x13BA0,
            0x11990,
            0x18CCC,
            0x18446,
            0x13B90,
            0x19DCC,
            0x10884,
            0x13B88,
            // 690
            0x11984,
            0x10882,
            0x11982,
            0x108D8,
            0x1846E,
            0x119D8,
            0x108CC,
            0x13BD8,
            0x119CC,
            0x108C6,
            // 700
            0x13BCC,
            0x119C6,
            0x108EE,
            0x119EE,
            0x13BEE,
            0x1EF50,
            0x1F7AC,
            0x1EF48,
            0x1F7A6,
            0x1EF44,
            // 710
            0x1EF42,
            0x1CE50,
            0x1E72C,
            0x1DED0,
            0x1EF6C,
            0x1E726,
            0x1DEC8,
            0x1EF66,
            0x1DEC4,
            0x1CE42,
            // 720
            0x1DEC2,
            0x18C50,
            0x1C62C,
            0x19CD0,
            0x18C48,
            0x1C626,
            0x1BDD0,
            0x19CC8,
            0x1CE66,
            0x1BDC8,
            // 730
            0x1DEE6,
            0x18C42,
            0x1BDC4,
            0x19CC2,
            0x1BDC2,
            0x10850,
            0x1842C,
            0x118D0,
            0x10848,
            0x18426,
            // 740
            0x139D0,
            0x118C8,
            0x18C66,
            0x17BD0,
            0x139C8,
            0x19CE6,
            0x10842,
            0x17BC8,
            0x1BDE6,
            0x118C2,
            // 750
            0x17BC4,
            0x1086C,
            0x118EC,
            0x10866,
            0x139EC,
            0x118E6,
            0x17BEC,
            0x139E6,
            0x17BE6,
            0x1EF28,
            // 760
            0x1F796,
            0x1EF24,
            0x1EF22,
            0x1CE28,
            0x1E716,
            0x1DE68,
            0x1EF36,
            0x1DE64,
            0x1CE22,
            0x1DE62,
            // 770
            0x18C28,
            0x1C616,
            0x19C68,
            0x18C24,
            0x1BCE8,
            0x19C64,
            0x18C22,
            0x1BCE4,
            0x19C62,
            0x1BCE2,
            // 780
            0x10828,
            0x18416,
            0x11868,
            0x18C36,
            0x138E8,
            0x11864,
            0x10822,
            0x179E8,
            0x138E4,
            0x11862,
            // 790
            0x179E4,
            0x138E2,
            0x179E2,
            0x11876,
            0x179F6,
            0x1EF12,
            0x1DE34,
            0x1DE32,
            0x19C34,
            0x1BC74,
            // 800
            0x1BC72,
            0x11834,
            0x13874,
            0x178F4,
            0x178F2,
            0x10540,
            0x10520,
            0x18298,
            0x10510,
            0x10508,
            // 810
            0x10504,
            0x105B0,
            0x10598,
            0x1058C,
            0x10586,
            0x105DC,
            0x105CE,
            0x186A0,
            0x18690,
            0x1C34C,
            // 820
            0x18688,
            0x1C346,
            0x18684,
            0x18682,
            0x104A0,
            0x18258,
            0x10DA0,
            0x186D8,
            0x1824C,
            0x10D90,
            // 830
            0x186CC,
            0x10D88,
            0x186C6,
            0x10D84,
            0x10482,
            0x10D82,
            0x104D8,
            0x1826E,
            0x10DD8,
            0x186EE,
            // 840
            0x10DCC,
            0x104C6,
            0x10DC6,
            0x104EE,
            0x10DEE,
            0x1C750,
            0x1C748,
            0x1C744,
            0x1C742,
            0x18650,
            // 850
            0x18ED0,
            0x1C76C,
            0x1C326,
            0x18EC8,
            0x1C766,
            0x18EC4,
            0x18642,
            0x18EC2,
            0x10450,
            0x10CD0,
            // 860
            0x10448,
            0x18226,
            0x11DD0,
            0x10CC8,
            0x10444,
            0x11DC8,
            0x10CC4,
            0x10442,
            0x11DC4,
            0x10CC2,
            // 870
            0x1046C,
            0x10CEC,
            0x10466,
            0x11DEC,
            0x10CE6,
            0x11DE6,
            0x1E7A8,
            0x1E7A4,
            0x1E7A2,
            0x1C728,
            // 880
            0x1CF68,
            0x1E7B6,
            0x1CF64,
            0x1C722,
            0x1CF62,
            0x18628,
            0x1C316,
            0x18E68,
            0x1C736,
            0x19EE8,
            // 890
            0x18E64,
            0x18622,
            0x19EE4,
            0x18E62,
            0x19EE2,
            0x10428,
            0x18216,
            0x10C68,
            0x18636,
            0x11CE8,
            // 900
            0x10C64,
            0x10422,
            0x13DE8,
            0x11CE4,
            0x10C62,
            0x13DE4,
            0x11CE2,
            0x10436,
            0x10C76,
            0x11CF6,
            // 910
            0x13DF6,
            0x1F7D4,
            0x1F7D2,
            0x1E794,
            0x1EFB4,
            0x1E792,
            0x1EFB2,
            0x1C714,
            0x1CF34,
            0x1C712,
            // 920
            0x1DF74,
            0x1CF32,
            0x1DF72,
            0x18614,
            0x18E34,
            0x18612,
            0x19E74,
            0x18E32,
            0x1BEF4,
        ],
        // 929
        [
            // cluster 3 -----------------------------------------------------------------------
            0x1F560,
            0x1FAB8,
            0x1EA40,
            0x1F530,
            0x1FA9C,
            0x1EA20,
            0x1F518,
            0x1FA8E,
            0x1EA10,
            0x1F50C,
            //  10
            0x1EA08,
            0x1F506,
            0x1EA04,
            0x1EB60,
            0x1F5B8,
            0x1FADE,
            0x1D640,
            0x1EB30,
            0x1F59C,
            0x1D620,
            //  20
            0x1EB18,
            0x1F58E,
            0x1D610,
            0x1EB0C,
            0x1D608,
            0x1EB06,
            0x1D604,
            0x1D760,
            0x1EBB8,
            0x1F5DE,
            //  30
            0x1AE40,
            0x1D730,
            0x1EB9C,
            0x1AE20,
            0x1D718,
            0x1EB8E,
            0x1AE10,
            0x1D70C,
            0x1AE08,
            0x1D706,
            //  40
            0x1AE04,
            0x1AF60,
            0x1D7B8,
            0x1EBDE,
            0x15E40,
            0x1AF30,
            0x1D79C,
            0x15E20,
            0x1AF18,
            0x1D78E,
            //  50
            0x15E10,
            0x1AF0C,
            0x15E08,
            0x1AF06,
            0x15F60,
            0x1AFB8,
            0x1D7DE,
            0x15F30,
            0x1AF9C,
            0x15F18,
            //  60
            0x1AF8E,
            0x15F0C,
            0x15FB8,
            0x1AFDE,
            0x15F9C,
            0x15F8E,
            0x1E940,
            0x1F4B0,
            0x1FA5C,
            0x1E920,
            //  70
            0x1F498,
            0x1FA4E,
            0x1E910,
            0x1F48C,
            0x1E908,
            0x1F486,
            0x1E904,
            0x1E902,
            0x1D340,
            0x1E9B0,
            //  80
            0x1F4DC,
            0x1D320,
            0x1E998,
            0x1F4CE,
            0x1D310,
            0x1E98C,
            0x1D308,
            0x1E986,
            0x1D304,
            0x1D302,
            //  90
            0x1A740,
            0x1D3B0,
            0x1E9DC,
            0x1A720,
            0x1D398,
            0x1E9CE,
            0x1A710,
            0x1D38C,
            0x1A708,
            0x1D386,
            // 100
            0x1A704,
            0x1A702,
            0x14F40,
            0x1A7B0,
            0x1D3DC,
            0x14F20,
            0x1A798,
            0x1D3CE,
            0x14F10,
            0x1A78C,
            // 110
            0x14F08,
            0x1A786,
            0x14F04,
            0x14FB0,
            0x1A7DC,
            0x14F98,
            0x1A7CE,
            0x14F8C,
            0x14F86,
            0x14FDC,
            // 120
            0x14FCE,
            0x1E8A0,
            0x1F458,
            0x1FA2E,
            0x1E890,
            0x1F44C,
            0x1E888,
            0x1F446,
            0x1E884,
            0x1E882,
            // 130
            0x1D1A0,
            0x1E8D8,
            0x1F46E,
            0x1D190,
            0x1E8CC,
            0x1D188,
            0x1E8C6,
            0x1D184,
            0x1D182,
            0x1A3A0,
            // 140
            0x1D1D8,
            0x1E8EE,
            0x1A390,
            0x1D1CC,
            0x1A388,
            0x1D1C6,
            0x1A384,
            0x1A382,
            0x147A0,
            0x1A3D8,
            // 150
            0x1D1EE,
            0x14790,
            0x1A3CC,
            0x14788,
            0x1A3C6,
            0x14784,
            0x14782,
            0x147D8,
            0x1A3EE,
            0x147CC,
            // 160
            0x147C6,
            0x147EE,
            0x1E850,
            0x1F42C,
            0x1E848,
            0x1F426,
            0x1E844,
            0x1E842,
            0x1D0D0,
            0x1E86C,
            // 170
            0x1D0C8,
            0x1E866,
            0x1D0C4,
            0x1D0C2,
            0x1A1D0,
            0x1D0EC,
            0x1A1C8,
            0x1D0E6,
            0x1A1C4,
            0x1A1C2,
            // 180
            0x143D0,
            0x1A1EC,
            0x143C8,
            0x1A1E6,
            0x143C4,
            0x143C2,
            0x143EC,
            0x143E6,
            0x1E828,
            0x1F416,
            // 190
            0x1E824,
            0x1E822,
            0x1D068,
            0x1E836,
            0x1D064,
            0x1D062,
            0x1A0E8,
            0x1D076,
            0x1A0E4,
            0x1A0E2,
            // 200
            0x141E8,
            0x1A0F6,
            0x141E4,
            0x141E2,
            0x1E814,
            0x1E812,
            0x1D034,
            0x1D032,
            0x1A074,
            0x1A072,
            // 210
            0x1E540,
            0x1F2B0,
            0x1F95C,
            0x1E520,
            0x1F298,
            0x1F94E,
            0x1E510,
            0x1F28C,
            0x1E508,
            0x1F286,
            // 220
            0x1E504,
            0x1E502,
            0x1CB40,
            0x1E5B0,
            0x1F2DC,
            0x1CB20,
            0x1E598,
            0x1F2CE,
            0x1CB10,
            0x1E58C,
            // 230
            0x1CB08,
            0x1E586,
            0x1CB04,
            0x1CB02,
            0x19740,
            0x1CBB0,
            0x1E5DC,
            0x19720,
            0x1CB98,
            0x1E5CE,
            // 240
            0x19710,
            0x1CB8C,
            0x19708,
            0x1CB86,
            0x19704,
            0x19702,
            0x12F40,
            0x197B0,
            0x1CBDC,
            0x12F20,
            // 250
            0x19798,
            0x1CBCE,
            0x12F10,
            0x1978C,
            0x12F08,
            0x19786,
            0x12F04,
            0x12FB0,
            0x197DC,
            0x12F98,
            // 260
            0x197CE,
            0x12F8C,
            0x12F86,
            0x12FDC,
            0x12FCE,
            0x1F6A0,
            0x1FB58,
            0x16BF0,
            0x1F690,
            0x1FB4C,
            // 270
            0x169F8,
            0x1F688,
            0x1FB46,
            0x168FC,
            0x1F684,
            0x1F682,
            0x1E4A0,
            0x1F258,
            0x1F92E,
            0x1EDA0,
            // 280
            0x1E490,
            0x1FB6E,
            0x1ED90,
            0x1F6CC,
            0x1F246,
            0x1ED88,
            0x1E484,
            0x1ED84,
            0x1E482,
            0x1ED82,
            // 290
            0x1C9A0,
            0x1E4D8,
            0x1F26E,
            0x1DBA0,
            0x1C990,
            0x1E4CC,
            0x1DB90,
            0x1EDCC,
            0x1E4C6,
            0x1DB88,
            // 300
            0x1C984,
            0x1DB84,
            0x1C982,
            0x1DB82,
            0x193A0,
            0x1C9D8,
            0x1E4EE,
            0x1B7A0,
            0x19390,
            0x1C9CC,
            // 310
            0x1B790,
            0x1DBCC,
            0x1C9C6,
            0x1B788,
            0x19384,
            0x1B784,
            0x19382,
            0x1B782,
            0x127A0,
            0x193D8,
            // 320
            0x1C9EE,
            0x16FA0,
            0x12790,
            0x193CC,
            0x16F90,
            0x1B7CC,
            0x193C6,
            0x16F88,
            0x12784,
            0x16F84,
            // 330
            0x12782,
            0x127D8,
            0x193EE,
            0x16FD8,
            0x127CC,
            0x16FCC,
            0x127C6,
            0x16FC6,
            0x127EE,
            0x1F650,
            // 340
            0x1FB2C,
            0x165F8,
            0x1F648,
            0x1FB26,
            0x164FC,
            0x1F644,
            0x1647E,
            0x1F642,
            0x1E450,
            0x1F22C,
            // 350
            0x1ECD0,
            0x1E448,
            0x1F226,
            0x1ECC8,
            0x1F666,
            0x1ECC4,
            0x1E442,
            0x1ECC2,
            0x1C8D0,
            0x1E46C,
            // 360
            0x1D9D0,
            0x1C8C8,
            0x1E466,
            0x1D9C8,
            0x1ECE6,
            0x1D9C4,
            0x1C8C2,
            0x1D9C2,
            0x191D0,
            0x1C8EC,
            // 370
            0x1B3D0,
            0x191C8,
            0x1C8E6,
            0x1B3C8,
            0x1D9E6,
            0x1B3C4,
            0x191C2,
            0x1B3C2,
            0x123D0,
            0x191EC,
            // 380
            0x167D0,
            0x123C8,
            0x191E6,
            0x167C8,
            0x1B3E6,
            0x167C4,
            0x123C2,
            0x167C2,
            0x123EC,
            0x167EC,
            // 390
            0x123E6,
            0x167E6,
            0x1F628,
            0x1FB16,
            0x162FC,
            0x1F624,
            0x1627E,
            0x1F622,
            0x1E428,
            0x1F216,
            // 400
            0x1EC68,
            0x1F636,
            0x1EC64,
            0x1E422,
            0x1EC62,
            0x1C868,
            0x1E436,
            0x1D8E8,
            0x1C864,
            0x1D8E4,
            // 410
            0x1C862,
            0x1D8E2,
            0x190E8,
            0x1C876,
            0x1B1E8,
            0x1D8F6,
            0x1B1E4,
            0x190E2,
            0x1B1E2,
            0x121E8,
            // 420
            0x190F6,
            0x163E8,
            0x121E4,
            0x163E4,
            0x121E2,
            0x163E2,
            0x121F6,
            0x163F6,
            0x1F614,
            0x1617E,
            // 430
            0x1F612,
            0x1E414,
            0x1EC34,
            0x1E412,
            0x1EC32,
            0x1C834,
            0x1D874,
            0x1C832,
            0x1D872,
            0x19074,
            // 440
            0x1B0F4,
            0x19072,
            0x1B0F2,
            0x120F4,
            0x161F4,
            0x120F2,
            0x161F2,
            0x1F60A,
            0x1E40A,
            0x1EC1A,
            // 450
            0x1C81A,
            0x1D83A,
            0x1903A,
            0x1B07A,
            0x1E2A0,
            0x1F158,
            0x1F8AE,
            0x1E290,
            0x1F14C,
            0x1E288,
            // 460
            0x1F146,
            0x1E284,
            0x1E282,
            0x1C5A0,
            0x1E2D8,
            0x1F16E,
            0x1C590,
            0x1E2CC,
            0x1C588,
            0x1E2C6,
            // 470
            0x1C584,
            0x1C582,
            0x18BA0,
            0x1C5D8,
            0x1E2EE,
            0x18B90,
            0x1C5CC,
            0x18B88,
            0x1C5C6,
            0x18B84,
            // 480
            0x18B82,
            0x117A0,
            0x18BD8,
            0x1C5EE,
            0x11790,
            0x18BCC,
            0x11788,
            0x18BC6,
            0x11784,
            0x11782,
            // 490
            0x117D8,
            0x18BEE,
            0x117CC,
            0x117C6,
            0x117EE,
            0x1F350,
            0x1F9AC,
            0x135F8,
            0x1F348,
            0x1F9A6,
            // 500
            0x134FC,
            0x1F344,
            0x1347E,
            0x1F342,
            0x1E250,
            0x1F12C,
            0x1E6D0,
            0x1E248,
            0x1F126,
            0x1E6C8,
            // 510
            0x1F366,
            0x1E6C4,
            0x1E242,
            0x1E6C2,
            0x1C4D0,
            0x1E26C,
            0x1CDD0,
            0x1C4C8,
            0x1E266,
            0x1CDC8,
            // 520
            0x1E6E6,
            0x1CDC4,
            0x1C4C2,
            0x1CDC2,
            0x189D0,
            0x1C4EC,
            0x19BD0,
            0x189C8,
            0x1C4E6,
            0x19BC8,
            // 530
            0x1CDE6,
            0x19BC4,
            0x189C2,
            0x19BC2,
            0x113D0,
            0x189EC,
            0x137D0,
            0x113C8,
            0x189E6,
            0x137C8,
            // 540
            0x19BE6,
            0x137C4,
            0x113C2,
            0x137C2,
            0x113EC,
            0x137EC,
            0x113E6,
            0x137E6,
            0x1FBA8,
            0x175F0,
            // 550
            0x1BAFC,
            0x1FBA4,
            0x174F8,
            0x1BA7E,
            0x1FBA2,
            0x1747C,
            0x1743E,
            0x1F328,
            0x1F996,
            0x132FC,
            // 560
            0x1F768,
            0x1FBB6,
            0x176FC,
            0x1327E,
            0x1F764,
            0x1F322,
            0x1767E,
            0x1F762,
            0x1E228,
            0x1F116,
            // 570
            0x1E668,
            0x1E224,
            0x1EEE8,
            0x1F776,
            0x1E222,
            0x1EEE4,
            0x1E662,
            0x1EEE2,
            0x1C468,
            0x1E236,
            // 580
            0x1CCE8,
            0x1C464,
            0x1DDE8,
            0x1CCE4,
            0x1C462,
            0x1DDE4,
            0x1CCE2,
            0x1DDE2,
            0x188E8,
            0x1C476,
            // 590
            0x199E8,
            0x188E4,
            0x1BBE8,
            0x199E4,
            0x188E2,
            0x1BBE4,
            0x199E2,
            0x1BBE2,
            0x111E8,
            0x188F6,
            // 600
            0x133E8,
            0x111E4,
            0x177E8,
            0x133E4,
            0x111E2,
            0x177E4,
            0x133E2,
            0x177E2,
            0x111F6,
            0x133F6,
            // 610
            0x1FB94,
            0x172F8,
            0x1B97E,
            0x1FB92,
            0x1727C,
            0x1723E,
            0x1F314,
            0x1317E,
            0x1F734,
            0x1F312,
            // 620
            0x1737E,
            0x1F732,
            0x1E214,
            0x1E634,
            0x1E212,
            0x1EE74,
            0x1E632,
            0x1EE72,
            0x1C434,
            0x1CC74,
            // 630
            0x1C432,
            0x1DCF4,
            0x1CC72,
            0x1DCF2,
            0x18874,
            0x198F4,
            0x18872,
            0x1B9F4,
            0x198F2,
            0x1B9F2,
            // 640
            0x110F4,
            0x131F4,
            0x110F2,
            0x173F4,
            0x131F2,
            0x173F2,
            0x1FB8A,
            0x1717C,
            0x1713E,
            0x1F30A,
            // 650
            0x1F71A,
            0x1E20A,
            0x1E61A,
            0x1EE3A,
            0x1C41A,
            0x1CC3A,
            0x1DC7A,
            0x1883A,
            0x1987A,
            0x1B8FA,
            // 660
            0x1107A,
            0x130FA,
            0x171FA,
            0x170BE,
            0x1E150,
            0x1F0AC,
            0x1E148,
            0x1F0A6,
            0x1E144,
            0x1E142,
            // 670
            0x1C2D0,
            0x1E16C,
            0x1C2C8,
            0x1E166,
            0x1C2C4,
            0x1C2C2,
            0x185D0,
            0x1C2EC,
            0x185C8,
            0x1C2E6,
            // 680
            0x185C4,
            0x185C2,
            0x10BD0,
            0x185EC,
            0x10BC8,
            0x185E6,
            0x10BC4,
            0x10BC2,
            0x10BEC,
            0x10BE6,
            // 690
            0x1F1A8,
            0x1F8D6,
            0x11AFC,
            0x1F1A4,
            0x11A7E,
            0x1F1A2,
            0x1E128,
            0x1F096,
            0x1E368,
            0x1E124,
            // 700
            0x1E364,
            0x1E122,
            0x1E362,
            0x1C268,
            0x1E136,
            0x1C6E8,
            0x1C264,
            0x1C6E4,
            0x1C262,
            0x1C6E2,
            // 710
            0x184E8,
            0x1C276,
            0x18DE8,
            0x184E4,
            0x18DE4,
            0x184E2,
            0x18DE2,
            0x109E8,
            0x184F6,
            0x11BE8,
            // 720
            0x109E4,
            0x11BE4,
            0x109E2,
            0x11BE2,
            0x109F6,
            0x11BF6,
            0x1F9D4,
            0x13AF8,
            0x19D7E,
            0x1F9D2,
            // 730
            0x13A7C,
            0x13A3E,
            0x1F194,
            0x1197E,
            0x1F3B4,
            0x1F192,
            0x13B7E,
            0x1F3B2,
            0x1E114,
            0x1E334,
            // 740
            0x1E112,
            0x1E774,
            0x1E332,
            0x1E772,
            0x1C234,
            0x1C674,
            0x1C232,
            0x1CEF4,
            0x1C672,
            0x1CEF2,
            // 750
            0x18474,
            0x18CF4,
            0x18472,
            0x19DF4,
            0x18CF2,
            0x19DF2,
            0x108F4,
            0x119F4,
            0x108F2,
            0x13BF4,
            // 760
            0x119F2,
            0x13BF2,
            0x17AF0,
            0x1BD7C,
            0x17A78,
            0x1BD3E,
            0x17A3C,
            0x17A1E,
            0x1F9CA,
            0x1397C,
            // 770
            0x1FBDA,
            0x17B7C,
            0x1393E,
            0x17B3E,
            0x1F18A,
            0x1F39A,
            0x1F7BA,
            0x1E10A,
            0x1E31A,
            0x1E73A,
            // 780
            0x1EF7A,
            0x1C21A,
            0x1C63A,
            0x1CE7A,
            0x1DEFA,
            0x1843A,
            0x18C7A,
            0x19CFA,
            0x1BDFA,
            0x1087A,
            // 790
            0x118FA,
            0x139FA,
            0x17978,
            0x1BCBE,
            0x1793C,
            0x1791E,
            0x138BE,
            0x179BE,
            0x178BC,
            0x1789E,
            // 800
            0x1785E,
            0x1E0A8,
            0x1E0A4,
            0x1E0A2,
            0x1C168,
            0x1E0B6,
            0x1C164,
            0x1C162,
            0x182E8,
            0x1C176,
            // 810
            0x182E4,
            0x182E2,
            0x105E8,
            0x182F6,
            0x105E4,
            0x105E2,
            0x105F6,
            0x1F0D4,
            0x10D7E,
            0x1F0D2,
            // 820
            0x1E094,
            0x1E1B4,
            0x1E092,
            0x1E1B2,
            0x1C134,
            0x1C374,
            0x1C132,
            0x1C372,
            0x18274,
            0x186F4,
            // 830
            0x18272,
            0x186F2,
            0x104F4,
            0x10DF4,
            0x104F2,
            0x10DF2,
            0x1F8EA,
            0x11D7C,
            0x11D3E,
            0x1F0CA,
            // 840
            0x1F1DA,
            0x1E08A,
            0x1E19A,
            0x1E3BA,
            0x1C11A,
            0x1C33A,
            0x1C77A,
            0x1823A,
            0x1867A,
            0x18EFA,
            // 850
            0x1047A,
            0x10CFA,
            0x11DFA,
            0x13D78,
            0x19EBE,
            0x13D3C,
            0x13D1E,
            0x11CBE,
            0x13DBE,
            0x17D70,
            // 860
            0x1BEBC,
            0x17D38,
            0x1BE9E,
            0x17D1C,
            0x17D0E,
            0x13CBC,
            0x17DBC,
            0x13C9E,
            0x17D9E,
            0x17CB8,
            // 870
            0x1BE5E,
            0x17C9C,
            0x17C8E,
            0x13C5E,
            0x17CDE,
            0x17C5C,
            0x17C4E,
            0x17C2E,
            0x1C0B4,
            0x1C0B2,
            // 880
            0x18174,
            0x18172,
            0x102F4,
            0x102F2,
            0x1E0DA,
            0x1C09A,
            0x1C1BA,
            0x1813A,
            0x1837A,
            0x1027A,
            // 890
            0x106FA,
            0x10EBE,
            0x11EBC,
            0x11E9E,
            0x13EB8,
            0x19F5E,
            0x13E9C,
            0x13E8E,
            0x11E5E,
            0x13EDE,
            // 900
            0x17EB0,
            0x1BF5C,
            0x17E98,
            0x1BF4E,
            0x17E8C,
            0x17E86,
            0x13E5C,
            0x17EDC,
            0x13E4E,
            0x17ECE,
            // 910
            0x17E58,
            0x1BF2E,
            0x17E4C,
            0x17E46,
            0x13E2E,
            0x17E6E,
            0x17E2C,
            0x17E26,
            0x10F5E,
            0x11F5C,
            // 920
            0x11F4E,
            0x13F58,
            0x19FAE,
            0x13F4C,
            0x13F46,
            0x11F2E,
            0x13F6E,
            0x13F2C,
            0x13F26,
        ],
        // 929
        [
            // cluster 6 -----------------------------------------------------------------------
            0x1ABE0,
            0x1D5F8,
            0x153C0,
            0x1A9F0,
            0x1D4FC,
            0x151E0,
            0x1A8F8,
            0x1D47E,
            0x150F0,
            0x1A87C,
            //  10
            0x15078,
            0x1FAD0,
            0x15BE0,
            0x1ADF8,
            0x1FAC8,
            0x159F0,
            0x1ACFC,
            0x1FAC4,
            0x158F8,
            0x1AC7E,
            //  20
            0x1FAC2,
            0x1587C,
            0x1F5D0,
            0x1FAEC,
            0x15DF8,
            0x1F5C8,
            0x1FAE6,
            0x15CFC,
            0x1F5C4,
            0x15C7E,
            //  30
            0x1F5C2,
            0x1EBD0,
            0x1F5EC,
            0x1EBC8,
            0x1F5E6,
            0x1EBC4,
            0x1EBC2,
            0x1D7D0,
            0x1EBEC,
            0x1D7C8,
            //  40
            0x1EBE6,
            0x1D7C4,
            0x1D7C2,
            0x1AFD0,
            0x1D7EC,
            0x1AFC8,
            0x1D7E6,
            0x1AFC4,
            0x14BC0,
            0x1A5F0,
            //  50
            0x1D2FC,
            0x149E0,
            0x1A4F8,
            0x1D27E,
            0x148F0,
            0x1A47C,
            0x14878,
            0x1A43E,
            0x1483C,
            0x1FA68,
            //  60
            0x14DF0,
            0x1A6FC,
            0x1FA64,
            0x14CF8,
            0x1A67E,
            0x1FA62,
            0x14C7C,
            0x14C3E,
            0x1F4E8,
            0x1FA76,
            //  70
            0x14EFC,
            0x1F4E4,
            0x14E7E,
            0x1F4E2,
            0x1E9E8,
            0x1F4F6,
            0x1E9E4,
            0x1E9E2,
            0x1D3E8,
            0x1E9F6,
            //  80
            0x1D3E4,
            0x1D3E2,
            0x1A7E8,
            0x1D3F6,
            0x1A7E4,
            0x1A7E2,
            0x145E0,
            0x1A2F8,
            0x1D17E,
            0x144F0,
            //  90
            0x1A27C,
            0x14478,
            0x1A23E,
            0x1443C,
            0x1441E,
            0x1FA34,
            0x146F8,
            0x1A37E,
            0x1FA32,
            0x1467C,
            // 100
            0x1463E,
            0x1F474,
            0x1477E,
            0x1F472,
            0x1E8F4,
            0x1E8F2,
            0x1D1F4,
            0x1D1F2,
            0x1A3F4,
            0x1A3F2,
            // 110
            0x142F0,
            0x1A17C,
            0x14278,
            0x1A13E,
            0x1423C,
            0x1421E,
            0x1FA1A,
            0x1437C,
            0x1433E,
            0x1F43A,
            // 120
            0x1E87A,
            0x1D0FA,
            0x14178,
            0x1A0BE,
            0x1413C,
            0x1411E,
            0x141BE,
            0x140BC,
            0x1409E,
            0x12BC0,
            // 130
            0x195F0,
            0x1CAFC,
            0x129E0,
            0x194F8,
            0x1CA7E,
            0x128F0,
            0x1947C,
            0x12878,
            0x1943E,
            0x1283C,
            // 140
            0x1F968,
            0x12DF0,
            0x196FC,
            0x1F964,
            0x12CF8,
            0x1967E,
            0x1F962,
            0x12C7C,
            0x12C3E,
            0x1F2E8,
            // 150
            0x1F976,
            0x12EFC,
            0x1F2E4,
            0x12E7E,
            0x1F2E2,
            0x1E5E8,
            0x1F2F6,
            0x1E5E4,
            0x1E5E2,
            0x1CBE8,
            // 160
            0x1E5F6,
            0x1CBE4,
            0x1CBE2,
            0x197E8,
            0x1CBF6,
            0x197E4,
            0x197E2,
            0x1B5E0,
            0x1DAF8,
            0x1ED7E,
            // 170
            0x169C0,
            0x1B4F0,
            0x1DA7C,
            0x168E0,
            0x1B478,
            0x1DA3E,
            0x16870,
            0x1B43C,
            0x16838,
            0x1B41E,
            // 180
            0x1681C,
            0x125E0,
            0x192F8,
            0x1C97E,
            0x16DE0,
            0x124F0,
            0x1927C,
            0x16CF0,
            0x1B67C,
            0x1923E,
            // 190
            0x16C78,
            0x1243C,
            0x16C3C,
            0x1241E,
            0x16C1E,
            0x1F934,
            0x126F8,
            0x1937E,
            0x1FB74,
            0x1F932,
            // 200
            0x16EF8,
            0x1267C,
            0x1FB72,
            0x16E7C,
            0x1263E,
            0x16E3E,
            0x1F274,
            0x1277E,
            0x1F6F4,
            0x1F272,
            // 210
            0x16F7E,
            0x1F6F2,
            0x1E4F4,
            0x1EDF4,
            0x1E4F2,
            0x1EDF2,
            0x1C9F4,
            0x1DBF4,
            0x1C9F2,
            0x1DBF2,
            // 220
            0x193F4,
            0x193F2,
            0x165C0,
            0x1B2F0,
            0x1D97C,
            0x164E0,
            0x1B278,
            0x1D93E,
            0x16470,
            0x1B23C,
            // 230
            0x16438,
            0x1B21E,
            0x1641C,
            0x1640E,
            0x122F0,
            0x1917C,
            0x166F0,
            0x12278,
            0x1913E,
            0x16678,
            // 240
            0x1B33E,
            0x1663C,
            0x1221E,
            0x1661E,
            0x1F91A,
            0x1237C,
            0x1FB3A,
            0x1677C,
            0x1233E,
            0x1673E,
            // 250
            0x1F23A,
            0x1F67A,
            0x1E47A,
            0x1ECFA,
            0x1C8FA,
            0x1D9FA,
            0x191FA,
            0x162E0,
            0x1B178,
            0x1D8BE,
            // 260
            0x16270,
            0x1B13C,
            0x16238,
            0x1B11E,
            0x1621C,
            0x1620E,
            0x12178,
            0x190BE,
            0x16378,
            0x1213C,
            // 270
            0x1633C,
            0x1211E,
            0x1631E,
            0x121BE,
            0x163BE,
            0x16170,
            0x1B0BC,
            0x16138,
            0x1B09E,
            0x1611C,
            // 280
            0x1610E,
            0x120BC,
            0x161BC,
            0x1209E,
            0x1619E,
            0x160B8,
            0x1B05E,
            0x1609C,
            0x1608E,
            0x1205E,
            // 290
            0x160DE,
            0x1605C,
            0x1604E,
            0x115E0,
            0x18AF8,
            0x1C57E,
            0x114F0,
            0x18A7C,
            0x11478,
            0x18A3E,
            // 300
            0x1143C,
            0x1141E,
            0x1F8B4,
            0x116F8,
            0x18B7E,
            0x1F8B2,
            0x1167C,
            0x1163E,
            0x1F174,
            0x1177E,
            // 310
            0x1F172,
            0x1E2F4,
            0x1E2F2,
            0x1C5F4,
            0x1C5F2,
            0x18BF4,
            0x18BF2,
            0x135C0,
            0x19AF0,
            0x1CD7C,
            // 320
            0x134E0,
            0x19A78,
            0x1CD3E,
            0x13470,
            0x19A3C,
            0x13438,
            0x19A1E,
            0x1341C,
            0x1340E,
            0x112F0,
            // 330
            0x1897C,
            0x136F0,
            0x11278,
            0x1893E,
            0x13678,
            0x19B3E,
            0x1363C,
            0x1121E,
            0x1361E,
            0x1F89A,
            // 340
            0x1137C,
            0x1F9BA,
            0x1377C,
            0x1133E,
            0x1373E,
            0x1F13A,
            0x1F37A,
            0x1E27A,
            0x1E6FA,
            0x1C4FA,
            // 350
            0x1CDFA,
            0x189FA,
            0x1BAE0,
            0x1DD78,
            0x1EEBE,
            0x174C0,
            0x1BA70,
            0x1DD3C,
            0x17460,
            0x1BA38,
            // 360
            0x1DD1E,
            0x17430,
            0x1BA1C,
            0x17418,
            0x1BA0E,
            0x1740C,
            0x132E0,
            0x19978,
            0x1CCBE,
            0x176E0,
            // 370
            0x13270,
            0x1993C,
            0x17670,
            0x1BB3C,
            0x1991E,
            0x17638,
            0x1321C,
            0x1761C,
            0x1320E,
            0x1760E,
            // 380
            0x11178,
            0x188BE,
            0x13378,
            0x1113C,
            0x17778,
            0x1333C,
            0x1111E,
            0x1773C,
            0x1331E,
            0x1771E,
            // 390
            0x111BE,
            0x133BE,
            0x177BE,
            0x172C0,
            0x1B970,
            0x1DCBC,
            0x17260,
            0x1B938,
            0x1DC9E,
            0x17230,
            // 400
            0x1B91C,
            0x17218,
            0x1B90E,
            0x1720C,
            0x17206,
            0x13170,
            0x198BC,
            0x17370,
            0x13138,
            0x1989E,
            // 410
            0x17338,
            0x1B99E,
            0x1731C,
            0x1310E,
            0x1730E,
            0x110BC,
            0x131BC,
            0x1109E,
            0x173BC,
            0x1319E,
            // 420
            0x1739E,
            0x17160,
            0x1B8B8,
            0x1DC5E,
            0x17130,
            0x1B89C,
            0x17118,
            0x1B88E,
            0x1710C,
            0x17106,
            // 430
            0x130B8,
            0x1985E,
            0x171B8,
            0x1309C,
            0x1719C,
            0x1308E,
            0x1718E,
            0x1105E,
            0x130DE,
            0x171DE,
            // 440
            0x170B0,
            0x1B85C,
            0x17098,
            0x1B84E,
            0x1708C,
            0x17086,
            0x1305C,
            0x170DC,
            0x1304E,
            0x170CE,
            // 450
            0x17058,
            0x1B82E,
            0x1704C,
            0x17046,
            0x1302E,
            0x1706E,
            0x1702C,
            0x17026,
            0x10AF0,
            0x1857C,
            // 460
            0x10A78,
            0x1853E,
            0x10A3C,
            0x10A1E,
            0x10B7C,
            0x10B3E,
            0x1F0BA,
            0x1E17A,
            0x1C2FA,
            0x185FA,
            // 470
            0x11AE0,
            0x18D78,
            0x1C6BE,
            0x11A70,
            0x18D3C,
            0x11A38,
            0x18D1E,
            0x11A1C,
            0x11A0E,
            0x10978,
            // 480
            0x184BE,
            0x11B78,
            0x1093C,
            0x11B3C,
            0x1091E,
            0x11B1E,
            0x109BE,
            0x11BBE,
            0x13AC0,
            0x19D70,
            // 490
            0x1CEBC,
            0x13A60,
            0x19D38,
            0x1CE9E,
            0x13A30,
            0x19D1C,
            0x13A18,
            0x19D0E,
            0x13A0C,
            0x13A06,
            // 500
            0x11970,
            0x18CBC,
            0x13B70,
            0x11938,
            0x18C9E,
            0x13B38,
            0x1191C,
            0x13B1C,
            0x1190E,
            0x13B0E,
            // 510
            0x108BC,
            0x119BC,
            0x1089E,
            0x13BBC,
            0x1199E,
            0x13B9E,
            0x1BD60,
            0x1DEB8,
            0x1EF5E,
            0x17A40,
            // 520
            0x1BD30,
            0x1DE9C,
            0x17A20,
            0x1BD18,
            0x1DE8E,
            0x17A10,
            0x1BD0C,
            0x17A08,
            0x1BD06,
            0x17A04,
            // 530
            0x13960,
            0x19CB8,
            0x1CE5E,
            0x17B60,
            0x13930,
            0x19C9C,
            0x17B30,
            0x1BD9C,
            0x19C8E,
            0x17B18,
            // 540
            0x1390C,
            0x17B0C,
            0x13906,
            0x17B06,
            0x118B8,
            0x18C5E,
            0x139B8,
            0x1189C,
            0x17BB8,
            0x1399C,
            // 550
            0x1188E,
            0x17B9C,
            0x1398E,
            0x17B8E,
            0x1085E,
            0x118DE,
            0x139DE,
            0x17BDE,
            0x17940,
            0x1BCB0,
            // 560
            0x1DE5C,
            0x17920,
            0x1BC98,
            0x1DE4E,
            0x17910,
            0x1BC8C,
            0x17908,
            0x1BC86,
            0x17904,
            0x17902,
            // 570
            0x138B0,
            0x19C5C,
            0x179B0,
            0x13898,
            0x19C4E,
            0x17998,
            0x1BCCE,
            0x1798C,
            0x13886,
            0x17986,
            // 580
            0x1185C,
            0x138DC,
            0x1184E,
            0x179DC,
            0x138CE,
            0x179CE,
            0x178A0,
            0x1BC58,
            0x1DE2E,
            0x17890,
            // 590
            0x1BC4C,
            0x17888,
            0x1BC46,
            0x17884,
            0x17882,
            0x13858,
            0x19C2E,
            0x178D8,
            0x1384C,
            0x178CC,
            // 600
            0x13846,
            0x178C6,
            0x1182E,
            0x1386E,
            0x178EE,
            0x17850,
            0x1BC2C,
            0x17848,
            0x1BC26,
            0x17844,
            // 610
            0x17842,
            0x1382C,
            0x1786C,
            0x13826,
            0x17866,
            0x17828,
            0x1BC16,
            0x17824,
            0x17822,
            0x13816,
            // 620
            0x17836,
            0x10578,
            0x182BE,
            0x1053C,
            0x1051E,
            0x105BE,
            0x10D70,
            0x186BC,
            0x10D38,
            0x1869E,
            // 630
            0x10D1C,
            0x10D0E,
            0x104BC,
            0x10DBC,
            0x1049E,
            0x10D9E,
            0x11D60,
            0x18EB8,
            0x1C75E,
            0x11D30,
            // 640
            0x18E9C,
            0x11D18,
            0x18E8E,
            0x11D0C,
            0x11D06,
            0x10CB8,
            0x1865E,
            0x11DB8,
            0x10C9C,
            0x11D9C,
            // 650
            0x10C8E,
            0x11D8E,
            0x1045E,
            0x10CDE,
            0x11DDE,
            0x13D40,
            0x19EB0,
            0x1CF5C,
            0x13D20,
            0x19E98,
            // 660
            0x1CF4E,
            0x13D10,
            0x19E8C,
            0x13D08,
            0x19E86,
            0x13D04,
            0x13D02,
            0x11CB0,
            0x18E5C,
            0x13DB0,
            // 670
            0x11C98,
            0x18E4E,
            0x13D98,
            0x19ECE,
            0x13D8C,
            0x11C86,
            0x13D86,
            0x10C5C,
            0x11CDC,
            0x10C4E,
            // 680
            0x13DDC,
            0x11CCE,
            0x13DCE,
            0x1BEA0,
            0x1DF58,
            0x1EFAE,
            0x1BE90,
            0x1DF4C,
            0x1BE88,
            0x1DF46,
            // 690
            0x1BE84,
            0x1BE82,
            0x13CA0,
            0x19E58,
            0x1CF2E,
            0x17DA0,
            0x13C90,
            0x19E4C,
            0x17D90,
            0x1BECC,
            // 700
            0x19E46,
            0x17D88,
            0x13C84,
            0x17D84,
            0x13C82,
            0x17D82,
            0x11C58,
            0x18E2E,
            0x13CD8,
            0x11C4C,
            // 710
            0x17DD8,
            0x13CCC,
            0x11C46,
            0x17DCC,
            0x13CC6,
            0x17DC6,
            0x10C2E,
            0x11C6E,
            0x13CEE,
            0x17DEE,
            // 720
            0x1BE50,
            0x1DF2C,
            0x1BE48,
            0x1DF26,
            0x1BE44,
            0x1BE42,
            0x13C50,
            0x19E2C,
            0x17CD0,
            0x13C48,
            // 730
            0x19E26,
            0x17CC8,
            0x1BE66,
            0x17CC4,
            0x13C42,
            0x17CC2,
            0x11C2C,
            0x13C6C,
            0x11C26,
            0x17CEC,
            // 740
            0x13C66,
            0x17CE6,
            0x1BE28,
            0x1DF16,
            0x1BE24,
            0x1BE22,
            0x13C28,
            0x19E16,
            0x17C68,
            0x13C24,
            // 750
            0x17C64,
            0x13C22,
            0x17C62,
            0x11C16,
            0x13C36,
            0x17C76,
            0x1BE14,
            0x1BE12,
            0x13C14,
            0x17C34,
            // 760
            0x13C12,
            0x17C32,
            0x102BC,
            0x1029E,
            0x106B8,
            0x1835E,
            0x1069C,
            0x1068E,
            0x1025E,
            0x106DE,
            // 770
            0x10EB0,
            0x1875C,
            0x10E98,
            0x1874E,
            0x10E8C,
            0x10E86,
            0x1065C,
            0x10EDC,
            0x1064E,
            0x10ECE,
            // 780
            0x11EA0,
            0x18F58,
            0x1C7AE,
            0x11E90,
            0x18F4C,
            0x11E88,
            0x18F46,
            0x11E84,
            0x11E82,
            0x10E58,
            // 790
            0x1872E,
            0x11ED8,
            0x18F6E,
            0x11ECC,
            0x10E46,
            0x11EC6,
            0x1062E,
            0x10E6E,
            0x11EEE,
            0x19F50,
            // 800
            0x1CFAC,
            0x19F48,
            0x1CFA6,
            0x19F44,
            0x19F42,
            0x11E50,
            0x18F2C,
            0x13ED0,
            0x19F6C,
            0x18F26,
            // 810
            0x13EC8,
            0x11E44,
            0x13EC4,
            0x11E42,
            0x13EC2,
            0x10E2C,
            0x11E6C,
            0x10E26,
            0x13EEC,
            0x11E66,
            // 820
            0x13EE6,
            0x1DFA8,
            0x1EFD6,
            0x1DFA4,
            0x1DFA2,
            0x19F28,
            0x1CF96,
            0x1BF68,
            0x19F24,
            0x1BF64,
            // 830
            0x19F22,
            0x1BF62,
            0x11E28,
            0x18F16,
            0x13E68,
            0x11E24,
            0x17EE8,
            0x13E64,
            0x11E22,
            0x17EE4,
            // 840
            0x13E62,
            0x17EE2,
            0x10E16,
            0x11E36,
            0x13E76,
            0x17EF6,
            0x1DF94,
            0x1DF92,
            0x19F14,
            0x1BF34,
            // 850
            0x19F12,
            0x1BF32,
            0x11E14,
            0x13E34,
            0x11E12,
            0x17E74,
            0x13E32,
            0x17E72,
            0x1DF8A,
            0x19F0A,
            // 860
            0x1BF1A,
            0x11E0A,
            0x13E1A,
            0x17E3A,
            0x1035C,
            0x1034E,
            0x10758,
            0x183AE,
            0x1074C,
            0x10746,
            // 870
            0x1032E,
            0x1076E,
            0x10F50,
            0x187AC,
            0x10F48,
            0x187A6,
            0x10F44,
            0x10F42,
            0x1072C,
            0x10F6C,
            // 880
            0x10726,
            0x10F66,
            0x18FA8,
            0x1C7D6,
            0x18FA4,
            0x18FA2,
            0x10F28,
            0x18796,
            0x11F68,
            0x18FB6,
            // 890
            0x11F64,
            0x10F22,
            0x11F62,
            0x10716,
            0x10F36,
            0x11F76,
            0x1CFD4,
            0x1CFD2,
            0x18F94,
            0x19FB4,
            // 900
            0x18F92,
            0x19FB2,
            0x10F14,
            0x11F34,
            0x10F12,
            0x13F74,
            0x11F32,
            0x13F72,
            0x1CFCA,
            0x18F8A,
            // 910
            0x19F9A,
            0x10F0A,
            0x11F1A,
            0x13F3A,
            0x103AC,
            0x103A6,
            0x107A8,
            0x183D6,
            0x107A4,
            0x107A2,
            // 920
            0x10396,
            0x107B6,
            0x187D4,
            0x187D2,
            0x10794,
            0x10FB4,
            0x10792,
            0x10FB2,
            0x1C7EA,
        ],
    ]; // end of $clusters array

    /**
     * Array of factors of the Reed-Solomon polynomial equations used for error correction; one sub array for each correction level (0-8).
     *
     * @protected
     */
    protected $rsfactors = [
        [
            // ECL 0 (2 factors) -------------------------------------------------------------------------------
            0x01B,
            0x395,
        ],
        //   2
        [
            // ECL 1 (4 factors) -------------------------------------------------------------------------------
            0x20A,
            0x238,
            0x2D3,
            0x329,
        ],
        //   4
        [
            // ECL 2 (8 factors) -------------------------------------------------------------------------------
            0x0ED,
            0x134,
            0x1B4,
            0x11C,
            0x286,
            0x28D,
            0x1AC,
            0x17B,
        ],
        //   8
        [
            // ECL 3 (16 factors) ------------------------------------------------------------------------------
            0x112,
            0x232,
            0x0E8,
            0x2F3,
            0x257,
            0x20C,
            0x321,
            0x084,
            0x127,
            0x074,
            0x1BA,
            0x1AC,
            0x127,
            0x02A,
            0x0B0,
            0x041,
        ],
        //  16
        [
            // ECL 4 (32 factors) ------------------------------------------------------------------------------
            0x169,
            0x23F,
            0x39A,
            0x20D,
            0x0B0,
            0x24A,
            0x280,
            0x141,
            0x218,
            0x2E6,
            0x2A5,
            0x2E6,
            0x2AF,
            0x11C,
            0x0C1,
            0x205,
            //  16
            0x111,
            0x1EE,
            0x107,
            0x093,
            0x251,
            0x320,
            0x23B,
            0x140,
            0x323,
            0x085,
            0x0E7,
            0x186,
            0x2AD,
            0x14A,
            0x03F,
            0x19A,
        ],
        //  32
        [
            // ECL 5 (64 factors) ------------------------------------------------------------------------------
            0x21B,
            0x1A6,
            0x006,
            0x05D,
            0x35E,
            0x303,
            0x1C5,
            0x06A,
            0x262,
            0x11F,
            0x06B,
            0x1F9,
            0x2DD,
            0x36D,
            0x17D,
            0x264,
            //  16
            0x2D3,
            0x1DC,
            0x1CE,
            0x0AC,
            0x1AE,
            0x261,
            0x35A,
            0x336,
            0x21F,
            0x178,
            0x1FF,
            0x190,
            0x2A0,
            0x2FA,
            0x11B,
            0x0B8,
            //  32
            0x1B8,
            0x023,
            0x207,
            0x01F,
            0x1CC,
            0x252,
            0x0E1,
            0x217,
            0x205,
            0x160,
            0x25D,
            0x09E,
            0x28B,
            0x0C9,
            0x1E8,
            0x1F6,
            //  48
            0x288,
            0x2DD,
            0x2CD,
            0x053,
            0x194,
            0x061,
            0x118,
            0x303,
            0x348,
            0x275,
            0x004,
            0x17D,
            0x34B,
            0x26F,
            0x108,
            0x21F,
        ],
        //  64
        [
            // ECL 6 (128 factors) -----------------------------------------------------------------------------
            0x209,
            0x136,
            0x360,
            0x223,
            0x35A,
            0x244,
            0x128,
            0x17B,
            0x035,
            0x30B,
            0x381,
            0x1BC,
            0x190,
            0x39D,
            0x2ED,
            0x19F,
            //  16
            0x336,
            0x05D,
            0x0D9,
            0x0D0,
            0x3A0,
            0x0F4,
            0x247,
            0x26C,
            0x0F6,
            0x094,
            0x1BF,
            0x277,
            0x124,
            0x38C,
            0x1EA,
            0x2C0,
            //  32
            0x204,
            0x102,
            0x1C9,
            0x38B,
            0x252,
            0x2D3,
            0x2A2,
            0x124,
            0x110,
            0x060,
            0x2AC,
            0x1B0,
            0x2AE,
            0x25E,
            0x35C,
            0x239,
            //  48
            0x0C1,
            0x0DB,
            0x081,
            0x0BA,
            0x0EC,
            0x11F,
            0x0C0,
            0x307,
            0x116,
            0x0AD,
            0x028,
            0x17B,
            0x2C8,
            0x1CF,
            0x286,
            0x308,
            //  64
            0x0AB,
            0x1EB,
            0x129,
            0x2FB,
            0x09C,
            0x2DC,
            0x05F,
            0x10E,
            0x1BF,
            0x05A,
            0x1FB,
            0x030,
            0x0E4,
            0x335,
            0x328,
            0x382,
            //  80
            0x310,
            0x297,
            0x273,
            0x17A,
            0x17E,
            0x106,
            0x17C,
            0x25A,
            0x2F2,
            0x150,
            0x059,
            0x266,
            0x057,
            0x1B0,
            0x29E,
            0x268,
            //  96
            0x09D,
            0x176,
            0x0F2,
            0x2D6,
            0x258,
            0x10D,
            0x177,
            0x382,
            0x34D,
            0x1C6,
            0x162,
            0x082,
            0x32E,
            0x24B,
            0x324,
            0x022,
            // 112
            0x0D3,
            0x14A,
            0x21B,
            0x129,
            0x33B,
            0x361,
            0x025,
            0x205,
            0x342,
            0x13B,
            0x226,
            0x056,
            0x321,
            0x004,
            0x06C,
            0x21B,
        ],
        // 128
        [
            // ECL 7 (256 factors) -----------------------------------------------------------------------------
            0x20C,
            0x37E,
            0x04B,
            0x2FE,
            0x372,
            0x359,
            0x04A,
            0x0CC,
            0x052,
            0x24A,
            0x2C4,
            0x0FA,
            0x389,
            0x312,
            0x08A,
            0x2D0,
            //  16
            0x35A,
            0x0C2,
            0x137,
            0x391,
            0x113,
            0x0BE,
            0x177,
            0x352,
            0x1B6,
            0x2DD,
            0x0C2,
            0x118,
            0x0C9,
            0x118,
            0x33C,
            0x2F5,
            //  32
            0x2C6,
            0x32E,
            0x397,
            0x059,
            0x044,
            0x239,
            0x00B,
            0x0CC,
            0x31C,
            0x25D,
            0x21C,
            0x391,
            0x321,
            0x2BC,
            0x31F,
            0x089,
            //  48
            0x1B7,
            0x1A2,
            0x250,
            0x29C,
            0x161,
            0x35B,
            0x172,
            0x2B6,
            0x145,
            0x0F0,
            0x0D8,
            0x101,
            0x11C,
            0x225,
            0x0D1,
            0x374,
            //  64
            0x13B,
            0x046,
            0x149,
            0x319,
            0x1EA,
            0x112,
            0x36D,
            0x0A2,
            0x2ED,
            0x32C,
            0x2AC,
            0x1CD,
            0x14E,
            0x178,
            0x351,
            0x209,
            //  80
            0x133,
            0x123,
            0x323,
            0x2C8,
            0x013,
            0x166,
            0x18F,
            0x38C,
            0x067,
            0x1FF,
            0x033,
            0x008,
            0x205,
            0x0E1,
            0x121,
            0x1D6,
            //  96
            0x27D,
            0x2DB,
            0x042,
            0x0FF,
            0x395,
            0x10D,
            0x1CF,
            0x33E,
            0x2DA,
            0x1B1,
            0x350,
            0x249,
            0x088,
            0x21A,
            0x38A,
            0x05A,
            // 112
            0x002,
            0x122,
            0x2E7,
            0x0C7,
            0x28F,
            0x387,
            0x149,
            0x031,
            0x322,
            0x244,
            0x163,
            0x24C,
            0x0BC,
            0x1CE,
            0x00A,
            0x086,
            // 128
            0x274,
            0x140,
            0x1DF,
            0x082,
            0x2E3,
            0x047,
            0x107,
            0x13E,
            0x176,
            0x259,
            0x0C0,
            0x25D,
            0x08E,
            0x2A1,
            0x2AF,
            0x0EA,
            // 144
            0x2D2,
            0x180,
            0x0B1,
            0x2F0,
            0x25F,
            0x280,
            0x1C7,
            0x0C1,
            0x2B1,
            0x2C3,
            0x325,
            0x281,
            0x030,
            0x03C,
            0x2DC,
            0x26D,
            // 160
            0x37F,
            0x220,
            0x105,
            0x354,
            0x28F,
            0x135,
            0x2B9,
            0x2F3,
            0x2F4,
            0x03C,
            0x0E7,
            0x305,
            0x1B2,
            0x1A5,
            0x2D6,
            0x210,
            // 176
            0x1F7,
            0x076,
            0x031,
            0x31B,
            0x020,
            0x090,
            0x1F4,
            0x0EE,
            0x344,
            0x18A,
            0x118,
            0x236,
            0x13F,
            0x009,
            0x287,
            0x226,
            // 192
            0x049,
            0x392,
            0x156,
            0x07E,
            0x020,
            0x2A9,
            0x14B,
            0x318,
            0x26C,
            0x03C,
            0x261,
            0x1B9,
            0x0B4,
            0x317,
            0x37D,
            0x2F2,
            // 208
            0x25D,
            0x17F,
            0x0E4,
            0x2ED,
            0x2F8,
            0x0D5,
            0x036,
            0x129,
            0x086,
            0x036,
            0x342,
            0x12B,
            0x39A,
            0x0BF,
            0x38E,
            0x214,
            // 224
            0x261,
            0x33D,
            0x0BD,
            0x014,
            0x0A7,
            0x01D,
            0x368,
            0x1C1,
            0x053,
            0x192,
            0x029,
            0x290,
            0x1F9,
            0x243,
            0x1E1,
            0x0AD,
            // 240
            0x194,
            0x0FB,
            0x2B0,
            0x05F,
            0x1F1,
            0x22B,
            0x282,
            0x21F,
            0x133,
            0x09F,
            0x39C,
            0x22E,
            0x288,
            0x037,
            0x1F1,
            0x00A,
        ],
        // 256
        [
            // ECL 8 (512 factors) -----------------------------------------------------------------------------
            0x160,
            0x04D,
            0x175,
            0x1F8,
            0x023,
            0x257,
            0x1AC,
            0x0CF,
            0x199,
            0x23E,
            0x076,
            0x1F2,
            0x11D,
            0x17C,
            0x15E,
            0x1EC,
            //  16
            0x0C5,
            0x109,
            0x398,
            0x09B,
            0x392,
            0x12B,
            0x0E5,
            0x283,
            0x126,
            0x367,
            0x132,
            0x058,
            0x057,
            0x0C1,
            0x160,
            0x30D,
            //  32
            0x34E,
            0x04B,
            0x147,
            0x208,
            0x1B3,
            0x21F,
            0x0CB,
            0x29A,
            0x0F9,
            0x15A,
            0x30D,
            0x26D,
            0x280,
            0x10C,
            0x31A,
            0x216,
            //  48
            0x21B,
            0x30D,
            0x198,
            0x186,
            0x284,
            0x066,
            0x1DC,
            0x1F3,
            0x122,
            0x278,
            0x221,
            0x025,
            0x35A,
            0x394,
            0x228,
            0x029,
            //  64
            0x21E,
            0x121,
            0x07A,
            0x110,
            0x17F,
            0x320,
            0x1E5,
            0x062,
            0x2F0,
            0x1D8,
            0x2F9,
            0x06B,
            0x310,
            0x35C,
            0x292,
            0x2E5,
            //  80
            0x122,
            0x0CC,
            0x2A9,
            0x197,
            0x357,
            0x055,
            0x063,
            0x03E,
            0x1E2,
            0x0B4,
            0x014,
            0x129,
            0x1C3,
            0x251,
            0x391,
            0x08E,
            //  96
            0x328,
            0x2AC,
            0x11F,
            0x218,
            0x231,
            0x04C,
            0x28D,
            0x383,
            0x2D9,
            0x237,
            0x2E8,
            0x186,
            0x201,
            0x0C0,
            0x204,
            0x102,
            // 112
            0x0F0,
            0x206,
            0x31A,
            0x18B,
            0x300,
            0x350,
            0x033,
            0x262,
            0x180,
            0x0A8,
            0x0BE,
            0x33A,
            0x148,
            0x254,
            0x312,
            0x12F,
            // 128
            0x23A,
            0x17D,
            0x19F,
            0x281,
            0x09C,
            0x0ED,
            0x097,
            0x1AD,
            0x213,
            0x0CF,
            0x2A4,
            0x2C6,
            0x059,
            0x0A8,
            0x130,
            0x192,
            // 144
            0x028,
            0x2C4,
            0x23F,
            0x0A2,
            0x360,
            0x0E5,
            0x041,
            0x35D,
            0x349,
            0x200,
            0x0A4,
            0x1DD,
            0x0DD,
            0x05C,
            0x166,
            0x311,
            // 160
            0x120,
            0x165,
            0x352,
            0x344,
            0x33B,
            0x2E0,
            0x2C3,
            0x05E,
            0x008,
            0x1EE,
            0x072,
            0x209,
            0x002,
            0x1F3,
            0x353,
            0x21F,
            // 176
            0x098,
            0x2D9,
            0x303,
            0x05F,
            0x0F8,
            0x169,
            0x242,
            0x143,
            0x358,
            0x31D,
            0x121,
            0x033,
            0x2AC,
            0x1D2,
            0x215,
            0x334,
            // 192
            0x29D,
            0x02D,
            0x386,
            0x1C4,
            0x0A7,
            0x156,
            0x0F4,
            0x0AD,
            0x023,
            0x1CF,
            0x28B,
            0x033,
            0x2BB,
            0x24F,
            0x1C4,
            0x242,
            // 208
            0x025,
            0x07C,
            0x12A,
            0x14C,
            0x228,
            0x02B,
            0x1AB,
            0x077,
            0x296,
            0x309,
            0x1DB,
            0x352,
            0x2FC,
            0x16C,
            0x242,
            0x38F,
            // 224
            0x11B,
            0x2C7,
            0x1D8,
            0x1A4,
            0x0F5,
            0x120,
            0x252,
            0x18A,
            0x1FF,
            0x147,
            0x24D,
            0x309,
            0x2BB,
            0x2B0,
            0x02B,
            0x198,
            // 240
            0x34A,
            0x17F,
            0x2D1,
            0x209,
            0x230,
            0x284,
            0x2CA,
            0x22F,
            0x03E,
            0x091,
            0x369,
            0x297,
            0x2C9,
            0x09F,
            0x2A0,
            0x2D9,
            // 256
            0x270,
            0x03B,
            0x0C1,
            0x1A1,
            0x09E,
            0x0D1,
            0x233,
            0x234,
            0x157,
            0x2B5,
            0x06D,
            0x260,
            0x233,
            0x16D,
            0x0B5,
            0x304,
            // 272
            0x2A5,
            0x136,
            0x0F8,
            0x161,
            0x2C4,
            0x19A,
            0x243,
            0x366,
            0x269,
            0x349,
            0x278,
            0x35C,
            0x121,
            0x218,
            0x023,
            0x309,
            // 288
            0x26A,
            0x24A,
            0x1A8,
            0x341,
            0x04D,
            0x255,
            0x15A,
            0x10D,
            0x2F5,
            0x278,
            0x2B7,
            0x2EF,
            0x14B,
            0x0F7,
            0x0B8,
            0x02D,
            // 304
            0x313,
            0x2A8,
            0x012,
            0x042,
            0x197,
            0x171,
            0x036,
            0x1EC,
            0x0E4,
            0x265,
            0x33E,
            0x39A,
            0x1B5,
            0x207,
            0x284,
            0x389,
            // 320
            0x315,
            0x1A4,
            0x131,
            0x1B9,
            0x0CF,
            0x12C,
            0x37C,
            0x33B,
            0x08D,
            0x219,
            0x17D,
            0x296,
            0x201,
            0x038,
            0x0FC,
            0x155,
            // 336
            0x0F2,
            0x31D,
            0x346,
            0x345,
            0x2D0,
            0x0E0,
            0x133,
            0x277,
            0x03D,
            0x057,
            0x230,
            0x136,
            0x2F4,
            0x299,
            0x18D,
            0x328,
            // 352
            0x353,
            0x135,
            0x1D9,
            0x31B,
            0x17A,
            0x01F,
            0x287,
            0x393,
            0x1CB,
            0x326,
            0x24E,
            0x2DB,
            0x1A9,
            0x0D8,
            0x224,
            0x0F9,
            // 368
            0x141,
            0x371,
            0x2BB,
            0x217,
            0x2A1,
            0x30E,
            0x0D2,
            0x32F,
            0x389,
            0x12F,
            0x34B,
            0x39A,
            0x119,
            0x049,
            0x1D5,
            0x317,
            // 384
            0x294,
            0x0A2,
            0x1F2,
            0x134,
            0x09B,
            0x1A6,
            0x38B,
            0x331,
            0x0BB,
            0x03E,
            0x010,
            0x1A9,
            0x217,
            0x150,
            0x11E,
            0x1B5,
            // 400
            0x177,
            0x111,
            0x262,
            0x128,
            0x0B7,
            0x39B,
            0x074,
            0x29B,
            0x2EF,
            0x161,
            0x03E,
            0x16E,
            0x2B3,
            0x17B,
            0x2AF,
            0x34A,
            // 416
            0x025,
            0x165,
            0x2D0,
            0x2E6,
            0x14A,
            0x005,
            0x027,
            0x39B,
            0x137,
            0x1A8,
            0x0F2,
            0x2ED,
            0x141,
            0x036,
            0x29D,
            0x13C,
            // 432
            0x156,
            0x12B,
            0x216,
            0x069,
            0x29B,
            0x1E8,
            0x280,
            0x2A0,
            0x240,
            0x21C,
            0x13C,
            0x1E6,
            0x2D1,
            0x262,
            0x02E,
            0x290,
            // 448
            0x1BF,
            0x0AB,
            0x268,
            0x1D0,
            0x0BE,
            0x213,
            0x129,
            0x141,
            0x2FA,
            0x2F0,
            0x215,
            0x0AF,
            0x086,
            0x00E,
            0x17D,
            0x1B1,
            // 464
            0x2CD,
            0x02D,
            0x06F,
            0x014,
            0x254,
            0x11C,
            0x2E0,
            0x08A,
            0x286,
            0x19B,
            0x36D,
            0x29D,
            0x08D,
            0x397,
            0x02D,
            0x30C,
            // 480
            0x197,
            0x0A4,
            0x14C,
            0x383,
            0x0A5,
            0x2D6,
            0x258,
            0x145,
            0x1F2,
            0x28F,
            0x165,
            0x2F0,
            0x300,
            0x0DF,
            0x351,
            0x287,
            // 496
            0x03F,
            0x136,
            0x35F,
            0x0FB,
            0x16E,
            0x130,
            0x11A,
            0x2E2,
            0x2A3,
            0x19A,
            0x185,
            0x0F4,
            0x01F,
            0x079,
            0x12F,
            0x107,
        ],
    ];

    /**
     * This is the class constructor.
     * Creates a PDF417 object.
     *
     * @param $code (string) code to represent using PDF417
     * @param $ecl (int) error correction level (0-8); default -1 = automatic correction level
     * @param $aspectratio (float) the width to height of the symbol (excluding quiet zones)
     * @param $macro (array) information for macro block
     *
     * @public
     */
    public function __construct($code, $ecl = -1, $aspectratio = 2, $macro = [])
    {
        $macrocw = [];
        $L = null;
        $barcode_array = [];
        if (is_null($code) or ('\0' == $code) or ('' == $code)) {
            return false;
        }
        // get the input sequence array
        $sequence = $this->getInputSequences($code);
        $codewords = []; // array of code-words
        foreach ($sequence as $seq) {
            $cw = $this->getCompaction($seq[0], $seq[1], true);
            $codewords = array_merge($codewords, $cw);
        }
        if (900 == $codewords[0]) {
            // Text Alpha is the default mode, so remove the first code
            array_shift($codewords);
        }
        // count number of codewords
        $numcw = count($codewords);
        if ($numcw > 925) {
            // reached maximum data codeword capacity
            return false;
        }
        // build macro control block codewords
        if (!empty($macro)) {
            $macrocw = [];
            // beginning of macro control block
            $macrocw[] = 928;
            // segment index
            $cw = $this->getCompaction(902, sprintf('%05d', $macro['segment_index']), false);
            $macrocw = array_merge($macrocw, $cw);
            // file ID
            $cw = $this->getCompaction(900, $macro['file_id'], false);
            $macrocw = array_merge($macrocw, $cw);
            // optional fields
            $optmodes = [900, 902, 902, 900, 900, 902, 902];
            $optsize = [-1, 2, 4, -1, -1, -1, 2];
            foreach ($optmodes as $k => $omode) {
                if (isset($macro['option_'.$k])) {
                    $macrocw[] = 923;
                    $macrocw[] = $k;
                    if (2 == $optsize[$k]) {
                        $macro['option_'.$k] = sprintf('%05d', $macro['option_'.$k]);
                    } elseif (4 == $optsize[$k]) {
                        $macro['option_'.$k] = sprintf('%010d', $macro['option_'.$k]);
                    }
                    $cw = $this->getCompaction($omode, $macro['option_'.$k], false);
                    $macrocw = array_merge($macrocw, $cw);
                }
            }
            if ($macro['segment_index'] == ($macro['segment_total'] - 1)) {
                // end of control block
                $macrocw[] = 922;
            }
            // update total codewords
            $numcw += count($macrocw);
        }
        // set error correction level
        $ecl = $this->getErrorCorrectionLevel($ecl, $numcw);
        // number of codewords for error correction
        $errsize = (2 << $ecl);
        // calculate number of columns (number of codewords per row) and rows
        $nce = ($numcw + $errsize + 1);
        $cols = round((sqrt(4761 + (68 * $aspectratio * ROWHEIGHT * $nce)) - 69) / 34);
        // adjust cols
        if ($cols < 1) {
            $cols = 1;
        } elseif ($cols > 30) {
            $cols = 30;
        }
        $rows = ceil($nce / $cols);
        $size = ($cols * $rows);
        // adjust rows
        if (($rows < 3) or ($rows > 90)) {
            if ($rows < 3) {
                $rows = 3;
            } elseif ($rows > 90) {
                $rows = 90;
            }
            $cols = ceil($size / $rows);
            $size = ($cols * $rows);
        }
        if ($size > 928) {
            // set dimensions to get maximum capacity
            if (abs($aspectratio - (17 * 29 / 32)) < abs($aspectratio - (17 * 16 / 58))) {
                $cols = 29;
                $rows = 32;
            } else {
                $cols = 16;
                $rows = 58;
            }
            $size = 928;
        }
        // calculate padding
        $pad = ($size - $nce);
        if ($pad > 0) {
            if (($size - $rows) == $nce) {
                --$rows;
                $size -= $rows;
            } else {
                // add pading
                $codewords = array_merge($codewords, array_fill(0, $pad, 900));
            }
        }
        if (!empty($macro)) {
            // add macro section
            $codewords = array_merge($codewords, $macrocw);
        }
        // Symbol Lenght Descriptor (number of data codewords including Symbol Lenght Descriptor and pad codewords)
        $sld = $size - $errsize;
        // add symbol length description
        array_unshift($codewords, $sld);
        // calculate error correction
        $ecw = $this->getErrorCorrection($codewords, $ecl);
        // add error correction codewords
        $codewords = array_merge($codewords, $ecw);
        // add horizontal quiet zones to start and stop patterns
        $pstart = str_repeat('0', QUIETH).$this->start_pattern;
        $pstop = $this->stop_pattern.str_repeat('0', QUIETH);
        $barcode_array['num_rows'] = ($rows * ROWHEIGHT) + (2 * QUIETV);
        $barcode_array['num_cols'] = (($cols + 2) * 17) + 35 + (2 * QUIETH);
        $barcode_array['bcode'] = [];
        // build rows for vertical quiet zone
        if (QUIETV > 0) {
            $empty_row = array_fill(0, $barcode_array['num_cols'], 0);
            for ($i = 0; $i < QUIETV; ++$i) {
                // add vertical quiet rows
                $barcode_array['bcode'][] = $empty_row;
            }
        }
        $k = 0; // codeword index
        $cid = 0; // initial cluster
        // for each row
        for ($r = 0; $r < $rows; ++$r) {
            // row start code
            $row = $pstart;
            switch ($cid) {
                case 0:
                    $L = ((30 * intval($r / 3)) + intval(($rows - 1) / 3));
                    break;

                case 1:
                    $L = ((30 * intval($r / 3)) + ($ecl * 3) + (($rows - 1) % 3));
                    break;

                case 2:
                    $L = ((30 * intval($r / 3)) + ($cols - 1));
                    break;
            }
            // left row indicator
            $row .= sprintf('%17b', $this->clusters[$cid][$L]);
            // for each column
            for ($c = 0; $c < $cols; ++$c) {
                $row .= sprintf('%17b', $this->clusters[$cid][$codewords[$k]]);
                ++$k;
            }
            switch ($cid) {
                case 0:
                    $L = ((30 * intval($r / 3)) + ($cols - 1));
                    break;

                case 1:
                    $L = ((30 * intval($r / 3)) + intval(($rows - 1) / 3));
                    break;

                case 2:
                    $L = ((30 * intval($r / 3)) + ($ecl * 3) + (($rows - 1) % 3));
                    break;
            }
            // right row indicator
            $row .= sprintf('%17b', $this->clusters[$cid][$L]);
            // row stop code
            $row .= $pstop;
            // convert the string to array
            $arow = preg_split('//', $row, -1, PREG_SPLIT_NO_EMPTY);
            // duplicate row to get the desired height
            for ($h = 0; $h < ROWHEIGHT; ++$h) {
                $barcode_array['bcode'][] = $arow;
            }
            ++$cid;
            if ($cid > 2) {
                $cid = 0;
            }
        }
        if (QUIETV > 0) {
            for ($i = 0; $i < QUIETV; ++$i) {
                // add vertical quiet rows
                $barcode_array['bcode'][] = $empty_row;
            }
        }
        $this->barcode_array = $barcode_array;
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
     * Returns the error correction level (0-8) to be used.
     *
     * @param $ecl (int) error correction level
     * @param $numcw (int) number of data codewords
     *
     * @return int error correction level
     *
     * @protected
     */
    protected function getErrorCorrectionLevel($ecl, $numcw)
    {
        // get maximum correction level
        $maxecl = 8; // starting error level
        $maxerrsize = (928 - $numcw); // available codewords for error
        while ($maxecl > 0) {
            $errsize = (2 << $ecl);
            if ($maxerrsize >= $errsize) {
                break;
            }
            --$maxecl;
        }
        // check for automatic levels
        if (($ecl < 0) or ($ecl > 8)) {
            if ($numcw < 41) {
                $ecl = 2;
            } elseif ($numcw < 161) {
                $ecl = 3;
            } elseif ($numcw < 321) {
                $ecl = 4;
            } elseif ($numcw < 864) {
                $ecl = 5;
            } else {
                $ecl = $maxecl;
            }
        }
        if ($ecl > $maxecl) {
            $ecl = $maxecl;
        }

        return $ecl;
    }

    /**
     * Returns the error correction codewords.
     *
     * @param $cw (array) array of codewords including Symbol Lenght Descriptor and pad
     * @param $ecl (int) error correction level 0-8
     *
     * @return array of error correction codewords
     *
     * @protected
     */
    protected function getErrorCorrection($cw, $ecl)
    {
        // get error correction coefficients
        $ecc = $this->rsfactors[$ecl];
        // number of error correction factors
        $eclsize = (2 << $ecl);
        // maximum index for $rsfactors[$ecl]
        $eclmaxid = ($eclsize - 1);
        // initialize array of error correction codewords
        $ecw = array_fill(0, $eclsize, 0);
        // for each data codeword
        foreach ($cw as $k => $d) {
            $t1 = ($d + $ecw[$eclmaxid]) % 929;
            for ($j = $eclmaxid; $j > 0; --$j) {
                $t2 = ($t1 * $ecc[$j]) % 929;
                $t3 = 929 - $t2;
                $ecw[$j] = ($ecw[$j - 1] + $t3) % 929;
            }
            $t2 = ($t1 * $ecc[0]) % 929;
            $t3 = 929 - $t2;
            $ecw[0] = $t3 % 929;
        }
        foreach ($ecw as $j => $e) {
            if (0 != $e) {
                $ecw[$j] = 929 - $e;
            }
        }
        $ecw = array_reverse($ecw);

        return $ecw;
    }

    /**
     * Create array of sequences from input.
     *
     * @param $code (string) code
     *
     * @return bidimensional array containing characters and classification
     *
     * @protected
     */
    protected function getInputSequences($code)
    {
        $sequence_array = []; // array to be returned
        $numseq = [];
        // get numeric sequences
        preg_match_all('/([0-9]{13,})/', $code, $numseq, PREG_OFFSET_CAPTURE);
        $numseq[1][] = ['', strlen($code)];
        $offset = 0;
        foreach ($numseq[1] as $seq) {
            $seqlen = strlen($seq[0]);
            if ($seq[1] > 0) {
                // extract text sequence before the number sequence
                $prevseq = substr($code, $offset, $seq[1] - $offset);
                $textseq = [];
                // get text sequences
                preg_match_all('/([\x09\x0a\x0d\x20-\x7e]{5,})/', $prevseq, $textseq, PREG_OFFSET_CAPTURE);
                $textseq[1][] = ['', strlen($prevseq)];
                $txtoffset = 0;
                foreach ($textseq[1] as $txtseq) {
                    $txtseqlen = strlen($txtseq[0]);
                    if ($txtseq[1] > 0) {
                        // extract byte sequence before the text sequence
                        $prevtxtseq = substr($prevseq, $txtoffset, $txtseq[1] - $txtoffset);
                        if (strlen($prevtxtseq) > 0) {
                            // add BYTE sequence
                            if ((1 == strlen($prevtxtseq)) and ((count($sequence_array) > 0) and (900 == $sequence_array[count($sequence_array) - 1][0]))) {
                                $sequence_array[] = [913, $prevtxtseq];
                            } elseif ((strlen($prevtxtseq) % 6) == 0) {
                                $sequence_array[] = [924, $prevtxtseq];
                            } else {
                                $sequence_array[] = [901, $prevtxtseq];
                            }
                        }
                    }
                    if ($txtseqlen > 0) {
                        // add numeric sequence
                        $sequence_array[] = [900, $txtseq[0]];
                    }
                    $txtoffset = $txtseq[1] + $txtseqlen;
                }
            }
            if ($seqlen > 0) {
                // add numeric sequence
                $sequence_array[] = [902, $seq[0]];
            }
            $offset = $seq[1] + $seqlen;
        }

        return $sequence_array;
    }

    /**
     * Compact data by mode.
     *
     * @param $mode (int) compaction mode number
     * @param $code (string) data to compact
     * @param $addmode (boolean) if true add the mode codeword at first position
     *
     * @return array of codewords
     *
     * @protected
     */
    protected function getCompaction($mode, $code, $addmode = true)
    {
        $cw = []; // array of codewords to return
        switch ($mode) {
            case 900:  // Text Compaction mode latch
                $submode = 0; // default Alpha sub-mode
                $txtarr = []; // array of characters and sub-mode switching characters
                $codelen = strlen($code);
                for ($i = 0; $i < $codelen; ++$i) {
                    $chval = ord($code[$i]);
                    if (($k = array_search($chval, $this->textsubmodes[$submode])) !== false) {
                        // we are on the same sub-mode
                        $txtarr[] = $k;
                    } else {
                        // the sub-mode is changed
                        for ($s = 0; $s < 4; ++$s) {
                            // search new sub-mode
                            if (($s != $submode) and (($k = array_search($chval, $this->textsubmodes[$s])) !== false)) {
                                // $s is the new submode
                                if (((($i + 1) == $codelen) or ((($i + 1) < $codelen) and (false !== array_search(ord($code[$i + 1]), $this->textsubmodes[$submode])))) and ((3 == $s) or ((0 == $s) and (1 == $submode)))) {
                                    // shift (temporary change only for this char)
                                    if (3 == $s) {
                                        // shift to puntuaction
                                        $txtarr[] = 29;
                                    } else {
                                        // shift from lower to alpha
                                        $txtarr[] = 27;
                                    }
                                } else {
                                    // latch
                                    $txtarr = array_merge($txtarr, $this->textlatch[''.$submode.$s]);
                                    // set new submode
                                    $submode = $s;
                                }
                                // add characted code to array
                                $txtarr[] = $k;
                                break;
                            }
                        }
                    }
                }
                $txtarrlen = count($txtarr);
                if (($txtarrlen % 2) != 0) {
                    // add padding
                    $txtarr[] = 29;
                    ++$txtarrlen;
                }
                // calculate codewords
                for ($i = 0; $i < $txtarrlen; $i += 2) {
                    $cw[] = (30 * $txtarr[$i]) + $txtarr[$i + 1];
                }
                break;

            case 901:
            case 924:  // Byte Compaction mode latch
                while (($codelen = strlen($code)) > 0) {
                    if ($codelen > 6) {
                        $rest = substr($code, 6);
                        $code = substr($code, 0, 6);
                        $sublen = 6;
                    } else {
                        $rest = '';
                        $sublen = strlen($code);
                    }
                    if (6 == $sublen) {
                        $t = bcmul(''.ord($code[0]), '1099511627776');
                        $t = bcadd($t, bcmul(''.ord($code[1]), '4294967296'));
                        $t = bcadd($t, bcmul(''.ord($code[2]), '16777216'));
                        $t = bcadd($t, bcmul(''.ord($code[3]), '65536'));
                        $t = bcadd($t, bcmul(''.ord($code[4]), '256'));
                        $t = bcadd($t, ''.ord($code[5]));
                        do {
                            $d = bcmod($t, '900');
                            $t = bcdiv($t, '900');
                            array_unshift($cw, $d);
                        } while ('0' != $t);
                    } else {
                        for ($i = 0; $i < $sublen; ++$i) {
                            $cw[] = ord($code[$i]);
                        }
                    }
                    $code = $rest;
                }
                break;

            case 902:  // Numeric Compaction mode latch
                while (($codelen = strlen($code)) > 0) {
                    if ($codelen > 44) {
                        $rest = substr($code, 44);
                        $code = substr($code, 0, 44);
                    } else {
                        $rest = '';
                    }
                    $t = '1'.$code;
                    do {
                        $d = bcmod($t, '900');
                        $t = bcdiv($t, '900');
                        array_unshift($cw, $d);
                    } while ('0' != $t);
                    $code = $rest;
                }
                break;

            case 913:  // Byte Compaction mode shift
                $cw[] = ord($code);
                break;
        }
        if ($addmode) {
            // add the compaction mode codeword at the beginning
            array_unshift($cw, $mode);
        }

        return $cw;
    }
}

// end PDF417 class

// ============================================================+
// END OF FILE
// ============================================================+
