<?php class Slippy {

/**
 * @param  array $lngLat - Array containing x (longitude) and y (latitude) in decimal degrees.
 * @param  int   $zoom   - Integer >= 0; greater value is more zoomed in.
 * @return array         - Array of 'z', 'x' and 'y' tile integers.
 */
public static function geoToTile (array $lngLat, int $zoom) {
  $lng = $lngLat[0];
  $lat = $lngLat[1];
  return [
    $zoom,
    floor((($lng + 180) / 360) * pow(2, $zoom)),
    floor((1 - log(tan(deg2rad($lat)) + 1 / cos(deg2rad($lat))) / pi()) /2 * pow(2, $zoom))
  ];
}

/**
 * @param  array $zxy - Array of 'z', 'x' and 'y' tile integers.
 * @return array      - Array containing x (longitude) and y (latitude) in decimal degrees of the northwest corner of the tile.
 */
public static function tileToGeo (array $zxy) {
  $z = $zxy[0];
  $x = $zxy[1];
  $y = $zxy[2];
  $n = pow(2, $z);
  return [
    $x / $n * 360.0 - 180.0,
    rad2deg(atan(sinh(pi() * (1 - 2 * $y / $n))))
  ];
}

/**
 * @param  array $zxy - Array of 'z', 'x' and 'y' tile integers.
 * @return array      - Array containing the west, south, east, and north bounds of the tile.
 */
public static function tileToBbox (array $zxy) {
  $z = $zxy[0];
  $x = $zxy[1];
  $y = $zxy[2];
  $n = pow(2, $z);
  return [
    $x / $n * 360.0 - 180.0,                            // xMin (West)
    rad2deg(atan(sinh(pi() * (1 - 2 * ($y+1) / $n)))),  // yMin (South)
    ($x+1) / $n * 360.0 - 180.0,                        // xMax (East)
    rad2deg(atan(sinh(pi() * (1 - 2 * $y / $n))))       // yMax (North)
  ];
}

/**
 * @param  array $zxy - Array of 'z', 'x' and 'y' tile integers.
 * @return array      - Array of x,y coordinates counterclockwise from Southwest corner, self-closing ring.
 */
public static function tileToPoly (array $zxy) {
  $bbox = self::tileToBbox($zxy);
  return [
    [$bbox[0], $bbox[1]], // SW
    [$bbox[2], $bbox[1]], // SE
    [$bbox[2], $bbox[3]], // NE
    [$bbox[0], $bbox[3]], // NW
    [$bbox[0], $bbox[1]]  // SW
  ];
}

/**
 * @param  array $zxy - Array of 'z', 'x' and 'y' tile integers.
 * @return array      - Array of the four zxy subtiles.
 */
public static function subtiles (array $zxy) {
  $zPlus = $zxy[0] + 1;
  $x2 = $zxy[1] * 2;
  $x2Plus = $x2 + 1;
  $y2 = $zxy[2] * 2;
  $y2Plus = $y2 + 1;
  return [
    [$zPlus, $x2,     $y2],
    [$zPlus, $x2Plus, $y2],
    [$zPlus, $x2,     $y2Plus],
    [$zPlus, $x2Plus, $y2Plus]
  ];
}

/**
 * @param  array $zxy  - Array of 'z', 'x' and 'y' tile integers.
 * @param  int   $zMax - (Optional) Target zoom level; defaults to z+1.
 * @return array - Array of the four zxy subtiles.
 */
public static function subsubtiles (array $zxy, $zMax=null) {
  $zPlus = $zxy[0] + 1;
  if ($zMax === null) $zMax = $zPlus;
  $subtiles = self::subtiles($zxy);
  if ($zPlus === $zMax) {
    return $subtiles;
  } else {
    $subsubtiles = [];
    foreach ($subtiles as $thisSubtile) {
      $subsubtiles = array_merge($subsubtiles, self::subsubtiles($thisSubtile, $zMax));
    }
    return $subsubtiles;
  }
}

/**
 * @param  array $zxy     - Array of 'z', 'x' and 'y' tile integers.
 * @param  int   $maxZoom - The maximum zoom level tiles to include.
 * @return array - Array of the four zxy subtiles.
 */
public static function subtiles_recursive (array $zxy, $maxZoom=10) {
  if ($zxy[0] < $maxZoom) {
    $subtiles = self::subtiles($zxy);
    if ($zxy[0] < ($maxZoom - 1)) {
      foreach ($subtiles as $tile) {
        $subtiles = array_merge($subtiles, self::subtiles_recursive($tile, $maxZoom));
      }
    }
    return $subtiles;
  }
}

}?>
