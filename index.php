<?php

/**
 * Get cache-busting hashed filename from assets.json.
 *
 * @param  string $filename Original name of the file.
 * @return string Current cache-busting hashed name of the file.
 */
function get_hashed_asset( $filename ) {

  // Cache the decoded manifest so that we only read it in once.
  static $manifest = null;
  if ( null === $manifest ) {
    $manifest_path = kirby::instance()->roots()->index() . '/dist/assets.json';
    $manifest = file_exists( $manifest_path )
      ? json_decode( file_get_contents( $manifest_path ), true )
      : [];
  }

  // Get rid of asset folder in path to match filename in manifest
  $filename = str_replace('dist/', '', $filename);

  // If the manifest contains the requested file, return the hashed name.
  if ( array_key_exists( $filename, $manifest ) ) {
    $hashed_filename = $manifest[ $filename ];
    // Add asset folder path to hashed file again
    return 'dist/' . $hashed_filename;
  }

  // Assume the file has not been hashed, when it was not found within the manifest and add asset folder path
  return 'dist/' . $filename;
}

// File path helper
function get_asset_path( $filename ) {
  return kirby::instance()->urls()->index() . '/' . get_hashed_asset($filename);
}

Kirby::plugin('kreativschnittstelle/cachebusting', [
  'components' => [
    'css' => function ($kirby, $url, $options) {
      return get_hashed_asset($url);
    },
    'js' => function ($kirby, $url, $options) {
      return get_hashed_asset($url);
    }
  ],
]);
