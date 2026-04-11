<?php











namespace Composer;

use Composer\Semver\VersionParser;






class InstalledVersions {


    private static $installed = array(
        'root'     => array(
            'pretty_version' => '1.0.0+no-version-set',
            'version'        => '1.0.0.0',
            'aliases'        => array(),
            'reference'      => null,
            'name'           => '__root__',
        ),
        'versions' => array(
            '__root__'                  => array(
                'pretty_version' => '1.0.0+no-version-set',
                'version'        => '1.0.0.0',
                'aliases'        => array(),
                'reference'      => null,
            ),
            'dompdf/dompdf'             => array(
                'pretty_version' => 'v1.1.1',
                'version'        => '1.1.1.0',
                'aliases'        => array(),
                'reference'      => 'de4aad040737a89fae2129cdeb0f79c45513128d',
            ),
            'phenx/php-font-lib'        => array(
                'pretty_version' => '0.5.4',
                'version'        => '0.5.4.0',
                'aliases'        => array(),
                'reference'      => 'dd448ad1ce34c63d09baccd05415e361300c35b4',
            ),
            'phenx/php-svg-lib'         => array(
                'pretty_version' => '0.3.4',
                'version'        => '0.3.4.0',
                'aliases'        => array(),
                'reference'      => 'f627771eb854aa7f45f80add0f23c6c4d67ea0f2',
            ),
            'sabberworm/php-css-parser' => array(
                'pretty_version' => '8.4.0',
                'version'        => '8.4.0.0',
                'aliases'        => array(),
                'reference'      => 'e41d2140031d533348b2192a83f02d8dd8a71d30',
            ),
        ),
    );


    public static function getInstalledPackages() {
         return array_keys( self::$installed['versions'] );

    }//end getInstalledPackages()


    public static function isInstalled( $packageName ) {
        return isset( self::$installed['versions'][ $packageName ] );

    }//end isInstalled()


    public static function satisfies( VersionParser $parser, $packageName, $constraint ) {
        $constraint = $parser->parseConstraints( $constraint );
        $provided   = $parser->parseConstraints( self::getVersionRanges( $packageName ) );

        return $provided->matches( $constraint );

    }//end satisfies()


    public static function getVersionRanges( $packageName ) {
        if ( !isset( self::$installed['versions'][ $packageName ] ) ) {
            throw new \OutOfBoundsException( 'Package "' . $packageName . '" is not installed' );
        }

        $ranges = array();
        if ( isset( self::$installed['versions'][ $packageName ]['pretty_version'] ) ) {
            $ranges[] = self::$installed['versions'][ $packageName ]['pretty_version'];
        }

        if ( array_key_exists( 'aliases', self::$installed['versions'][ $packageName ] ) ) {
            $ranges = array_merge( $ranges, self::$installed['versions'][ $packageName ]['aliases'] );
        }

        if ( array_key_exists( 'replaced', self::$installed['versions'][ $packageName ] ) ) {
            $ranges = array_merge( $ranges, self::$installed['versions'][ $packageName ]['replaced'] );
        }

        if ( array_key_exists( 'provided', self::$installed['versions'][ $packageName ] ) ) {
            $ranges = array_merge( $ranges, self::$installed['versions'][ $packageName ]['provided'] );
        }

        return implode( ' || ', $ranges );

    }//end getVersionRanges()


    public static function getVersion( $packageName ) {
        if ( !isset( self::$installed['versions'][ $packageName ] ) ) {
            throw new \OutOfBoundsException( 'Package "' . $packageName . '" is not installed' );
        }

        if ( !isset( self::$installed['versions'][ $packageName ]['version'] ) ) {
            return null;
        }

        return self::$installed['versions'][ $packageName ]['version'];

    }//end getVersion()


    public static function getPrettyVersion( $packageName ) {
        if ( !isset( self::$installed['versions'][ $packageName ] ) ) {
            throw new \OutOfBoundsException( 'Package "' . $packageName . '" is not installed' );
        }

        if ( !isset( self::$installed['versions'][ $packageName ]['pretty_version'] ) ) {
            return null;
        }

        return self::$installed['versions'][ $packageName ]['pretty_version'];

    }//end getPrettyVersion()


    public static function getReference( $packageName ) {
        if ( !isset( self::$installed['versions'][ $packageName ] ) ) {
            throw new \OutOfBoundsException( 'Package "' . $packageName . '" is not installed' );
        }

        if ( !isset( self::$installed['versions'][ $packageName ]['reference'] ) ) {
            return null;
        }

        return self::$installed['versions'][ $packageName ]['reference'];

    }//end getReference()


    public static function getRootPackage() {
         return self::$installed['root'];

    }//end getRootPackage()


    public static function getRawData() {
         return self::$installed;

    }//end getRawData()


    public static function reload( $data ) {
        self::$installed = $data;

    }//end reload()


}//end class
