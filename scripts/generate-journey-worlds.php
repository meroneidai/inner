<?php

/**
 * Generates cohesive InnerPath journey atmosphere plates (PNG → encode with avifenc/cwebp).
 * Usage: php scripts/generate-journey-worlds.php
 */

declare(strict_types=1);

function journeyClamp(float $v, float $a, float $b): float
{
    return max($a, min($b, $v));
}

/** @param array{0:float,1:float,2:float} $a @param array{0:float,1:float,2:float} $b @return array{0:float,1:float,2:float} */
function journeyMix(array $a, array $b, float $t): array
{
    return [
        $a[0] + ($b[0] - $a[0]) * $t,
        $a[1] + ($b[1] - $a[1]) * $t,
        $a[2] + ($b[2] - $a[2]) * $t,
    ];
}

function journeyNoise(float $x, float $y, float $seed): float
{
    $n = sin($x * 12.9898 + $y * 78.233 + $seed) * 43758.5453;

    return $n - floor($n);
}

function journeyFbm(float $x, float $y, float $seed): float
{
    $v = 0.0;
    $a = 0.5;
    for ($i = 0; $i < 4; $i++) {
        $v += $a * journeyNoise($x, $y, $seed + $i * 19.1);
        $x *= 2.1;
        $y *= 2.1;
        $a *= 0.5;
    }

    return $v;
}

function journeySmoothstep(float $e0, float $e1, float $x): float
{
    $t = journeyClamp(($x - $e0) / ($e1 - $e0), 0.0, 1.0);

    return $t * $t * (3.0 - 2.0 * $t);
}

$outDir = dirname(__DIR__).'/public/images/journey';
$tmpDir = sys_get_temp_dir();

$worlds = [
    ['chest', [5, 5, 8], [40, 22, 12], [214, 183, 122], 'radial-tunnel'],
    ['desert', [42, 24, 10], [120, 70, 28], [230, 190, 120], 'dunes'],
    ['ice', [7, 17, 29], [20, 60, 90], [140, 220, 230], 'crystals'],
    ['forest', [12, 18, 10], [30, 50, 22], [220, 160, 80], 'roots'],
    ['ocean', [4, 16, 24], [10, 70, 100], [110, 200, 230], 'depth'],
    ['diamond', [18, 22, 32], [40, 50, 70], [255, 250, 240], 'crystal'],
    ['peace', [180, 195, 210], [230, 235, 240], [255, 250, 240], 'horizon'],
];

$width = 960;
$height = 540;

foreach ($worlds as $wi => [$id, $cA, $cB, $cAccent, $motif]) {
    $im = imagecreatetruecolor($width, $height);
    imagealphablending($im, true);

    for ($y = 0; $y < $height; $y++) {
        for ($x = 0; $x < $width; $x++) {
            $u = $x / ($width - 1);
            $v = $y / ($height - 1);
            $dx = $u - 0.5;
            $dy = $v - 0.45;
            $r = sqrt($dx * $dx + $dy * $dy);
            $n = journeyFbm($u * 3.2, $v * 2.4, $wi * 17.3);
            $base = journeyMix($cA, $cB, journeyClamp($v * 0.75 + $n * 0.25, 0, 1));

            if ($motif === 'radial-tunnel' || $motif === 'crystal' || $motif === 'depth') {
                $glow = exp(-pow($r * 2.4, 2));
                $base = journeyMix($base, $cAccent, $glow * 0.55);
                $rings = 0.5 + 0.5 * sin(($r * 28) - $n * 6);
                $base = journeyMix($base, [255, 255, 255], $rings * $glow * 0.12);
            } elseif ($motif === 'dunes') {
                $dune = 0.5 + 0.5 * sin(($u * 8 + $n) * 3.2 + $v * 2);
                $ridge = pow(max(0, sin(($u * 6) + $v * 3 + $n)), 3);
                $base = journeyMix($base, $cAccent, $dune * 0.35 + $ridge * 0.25);
                $base = journeyMix($base, $cA, pow($v, 1.4) * 0.35);
            } elseif ($motif === 'crystals') {
                $spire = exp(-pow(($u - 0.5) * 4.5, 2)) * exp(-pow(($v - 0.35) * 2.2, 2));
                $facet = abs(sin($u * 40 + $n * 8) * cos($v * 18));
                $base = journeyMix($base, $cAccent, $spire * 0.55 + $facet * 0.12);
                $base = journeyMix($base, [230, 245, 255], exp(-pow($r * 1.8, 2)) * 0.25);
            } elseif ($motif === 'roots') {
                $root = exp(-abs(sin($u * 9 + $v * 2) - ($v * 0.6)) * 8);
                $orb = exp(-pow(($u - 0.3) * 10, 2) - pow(($v - 0.55) * 8, 2))
                    + exp(-pow(($u - 0.7) * 11, 2) - pow(($v - 0.4) * 9, 2));
                $base = journeyMix($base, $cAccent, $root * 0.35 + $orb * 0.55);
            } elseif ($motif === 'horizon') {
                $band = journeySmoothstep(0.42, 0.62, $v);
                $sun = exp(-pow($r * 3.2, 2));
                $base = journeyMix($cA, $cB, $band);
                $base = journeyMix($base, $cAccent, $sun * 0.7);
                $mist = journeyFbm($u * 1.5, $v * 1.2, $wi) * 0.15;
                $base = journeyMix($base, [255, 255, 255], $mist + (1 - $v) * 0.08);
            }

            $vig = 1 - pow($r * 1.15, 2) * 0.45;
            $grain = (journeyNoise($x * 0.7, $y * 0.7, $wi) - 0.5) * 8;
            $rr = (int) journeyClamp($base[0] * $vig + $grain, 0, 255);
            $gg = (int) journeyClamp($base[1] * $vig + $grain, 0, 255);
            $bb = (int) journeyClamp($base[2] * $vig + $grain, 0, 255);
            imagesetpixel($im, $x, $y, imagecolorallocate($im, $rr, $gg, $bb));
        }
    }

    for ($i = 0; $i < 48; $i++) {
        $px = random_int(20, $width - 20);
        $py = random_int(20, $height - 20);
        $rad = random_int(2, 7);
        $col = imagecolorallocatealpha($im, $cAccent[0], $cAccent[1], $cAccent[2], 90);
        imagefilledellipse($im, $px, $py, $rad, $rad, $col);
    }

    $png = "{$tmpDir}/journey-{$id}.png";
    imagepng($im, $png, 6);
    echo "wrote {$png}\n";
}

echo "Next: avifenc -s 4 -q 58 /tmp/journey-ID.png public/images/journey/world-ID.avif\n";
