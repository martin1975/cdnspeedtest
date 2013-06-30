<?php
// CDN Speed Test
//
// This PHP script is intended to compare various configurations of the OVH CDN,
// in terms of page loading performance on real-life browsers.
//
// For now, this script relies on ChromeDriver, a Google Chrome WebDriver
// implementation. However, with little modification (change the $wdHost
// and BROWSER_NAME), you can use any other WebDriver compatible browser
// (including Firefox, Internet Explorer and Opera).
//
// -------------------------------------------------------------------------------
//
// Except when otherwise stated, this test suite written by martin@korolczuk.net
// in 2013 is in public domain. Do whatever you want with this!
//
// www/css/pure-min.css: (c) Pure v0.1.0 - Copyright 2013 Yahoo! Inc. All rights reserved.
//
// webdriver/php-webdriver: Copyright 2004-present Facebook. All Rights Reserved. - Licensed under the Apache License, Version 2.0
//
// standard_deviation.php: (c) Pascal Woerde

// Include facebook/php-webdriver (with all needed includes)
require_once( __DIR__ . '/php-webdriver.php' );

// Standard deviation
require_once( __DIR__ . '/standard_deviation.php' );



// ====================================================================================
// Data
// ====================================================================================

// WebDriver drivers to connect to
$webDrivers = array(
    // ChromeDriver
    // ChromeDriver is the WebDriver implementation for Chrome,
    // see https://code.google.com/p/selenium/wiki/ChromeDriver
    array( 'driver' => 'http://localhost:9515', 'browser' => 'chrome' ),

    // The following drivers come with Selenium Server (aka Selenium,
    // Selenium 2.0 or Selenium WebDriver), see http://docs.seleniumhq.org/download/
    // All you have to do is to install those browsers and run Selenium Server.
    //array( 'driver' => 'http://localhost:4444/wd/hub', 'browser' => 'firefox', 'best' => 0x7fffffff ),
    //array( 'driver' => 'http://localhost:4444/wd/hub', 'browser' => 'opera', 'best' => 0x7fffffff ),
    //array( 'driver' => 'http://localhost:4444/wd/hub', 'browser' => 'safari', 'best' => 0x7fffffff ) // safari crashes often
);


// Web pages to test
$pages = array(
    /*
    // Those may be used as reference
    array( 'url' => 'http://unicodesnowmanforyou.com/',   'time' => array() ),
    array( 'url' => 'http://perdu.com/', 'time' => array() ),
    array( 'url' => 'http://xn--ls8h.la/', 'time' => array() ),
    array( 'url' => 'http://www.ovh.com/fr/index.xml', 'time' => array() ),
*/

    // The "from-host.html" (from the "test" host) page loads:
    // - HTML from the current "test" host (server),
    // - resources (CSS, PNGs) from "test" host too (server).
    array( 'url' => 'http://test.korolczuk.fr/from-host.html',
           'time' => array() ),

    // The "from-server.html" page loads:
    // - HTML from the "test" host (server);
    // - resources (CSS, PNGs) from "server.test" host (server).
    array( 'url' => 'http://test.korolczuk.fr/from-server.html',
           'time' => array() ),

    // The "from-host.html" (from the "cdn.test" host) page loads:
    // - HTML from the current "cdn.test" host (CDN);
    // - resources (CSS, PNGs) from "cdn.test" host too (CDN).
    array( 'url' => 'http://cdn.test.korolczuk.fr/from-host.html',
           'time' => array() ),

    // The "from-cdn.html" page loads:
    // - HTML from the "test" host (server);
    // - resources (CSS, PNGs) from "cdn.test" host (CDN).
    array( 'url' => 'http://test.korolczuk.fr/from-cdn.html',
           'time' => array() )
);


// Number of test iterations
$loops = 10;

// Default URL used for ensure full browser initialization
$urlDefault = 'http://www.google.fr/';



// ====================================================================================
// Initialization
// ====================================================================================



// ====================================================================================
// Process
// ====================================================================================


// Initialize global timer (used for progress display)
$globalTimerStart = microtime( true );

// Perform tests
for( $i = 1; $i <= $loops; $i++ )
{
    // Progress display (1/2)
    printf( "Performing test #%3d...", $i );

    // Randomize order in order to prevent any order-specific side effects
    shuffle( $webDrivers );
    shuffle( $pages );

    // Test all browsers
    foreach( $webDrivers as $webDriver )
    {
        $browser = $webDriver[ 'browser' ];

        // Progress
        echo( ' ' . $browser );

        // Test all pages
        foreach( $pages as &$page )
        {
            // Launch browser (with no cookies and empty cache)
            $wd = new WebDriver(
                $webDriver[ 'driver' ],
                array( WebDriverCapabilityType::BROWSER_NAME => $browser ) );

            // Go somewhere (a very light web page, please)
            // We need this in order to perform all browser internal initializations:
            // the first page loaded whatever it is, is much more slower than the next
            // ones.
            $wd->get( $urlDefault );

            // Wait a bit to avoid any lazy initialization has impact on our test
            // Best times vary between 1 and 2 seconds.
            // Below 1 second, there is no enough time for the browser to finish
            // whatever it does after loading the first page. This makes loading
            // the second page slower than it should be.
            // Above 2 seconds, the browser appears to pass to a kind of sleep
            // mode, making next page load to be slower.
            // Those observations are empirical, based on observations on my computer.
            sleep( 1 );

            // Perform test (with timer): fully load the given page
            $timerStart = microtime( true );

            $wd->get( $page[ 'url' ] );

            $timerStop = microtime( true );
            $timeElapsed = $timerStop - $timerStart;
            if( !isset( $page[ 'time' ][ $browser ] ) )
            {
                $page[ 'time' ][ $browser ] = array();
                $page[ 'time' ][ $browser ][ 'times' ] = array();
            }
            $page[ 'time' ][ $browser ][ 'times' ][] = $timeElapsed;

            // Wait a bit to finish asynchronous loading and other lazy browser magic
            // Apparently, Firefox dislike to quit abruptly.
            // Firefox suggests to restart in safe mode, while Safari crashes from
            // time to time. Let be gentle with those little things!
            sleep( 1 );

            // Quit browser
            // We need a fresh one, without cookies and cache, for the next run.
            // (There is no way to delete cookies and cache on WebDriver. It
            // is done in a browser- and system-specific way. It's easier and
            // more consistent to shut down the browser and restart it with a
            // new, fresh user profile.)
            $wd->quit();
            unset( $wd );

            // Progress
            echo( '.' );
        }
        unset( $page ); // Unset reference to avoid bugs
    }

    // Progress display (2/2)
    $globalTimerCurrent = microtime( true );
    $globalTimerElapsed = $globalTimerCurrent - $globalTimerStart;
    $globalTimerTotal   = ( $globalTimerElapsed / $i ) * $loops;
    $globalTimerLeft    = $globalTimerTotal - $globalTimerElapsed;

    $minutes = floor( $globalTimerLeft / 60.0 );
    $seconds = floor( $globalTimerLeft % 60.0 );

    printf( " Time to go: %2dm%02ds\n", $minutes, $seconds );
}




// Display results

// Always display results in the same order (whatever it is, just use the same)
sort( $webDrivers );
sort( $pages );

echo( "\n" . serialize( $webDrivers ) . "\n" );
echo( "\n" . serialize( $pages ) . "\n" );






// Display raw results (then delete those outside a 95% confidence interval)
echo( "\n\nLoading time in milliseconds (ms):\n\n" );

foreach( $pages as $page )
{
    foreach( $webDrivers as $webDriver )
    {
        $browser = $webDriver[ 'browser' ];
        $times = $page[ 'time' ][ $browser ][ 'times' ];
        foreach( $times as $time )
            printf( "%-46s\t%-14s\t%.3f\n", $page[ 'url' ], $browser, $time );
    }
}

foreach( $webDrivers as &$webDriver )
{
    $browser = $webDriver[ 'browser' ];
    foreach( $pages as &$page )
    {
        $times = &$page[ 'time' ][ $browser ][ 'times' ];

        // Data mean
        $mean = array_sum( $times ) / count( $times );

        // Standard deviation
        $standardDeviation = standard_deviation( $times );

        // Remove data outside a 95% confidence interval
        foreach( $times as $keyTime => $time )
        {
            // Is the data inside the 95% confidence interval?
            if(    $time < $mean - $standardDeviation * 1.96
                || $time > $mean + $standardDeviation * 1.96 )
            {
                // No, it isn't. Forget it!
                // (You can't unset a referenced variable, and setting it to NULL
                // does not remove this value from the array.)
                unset( $times[ $keyTime ] );
            }
        }

        if( !isset( $webDriver[ 'time' ] ) )          $webDriver[ 'time' ] = array();
        if( !isset( $webDriver[ 'time' ][ 'min' ] ) ) $webDriver[ 'time' ][ 'min' ] = 0x7fffffff;
        //if( !isset( $webDriver[ 'time' ][ 'max' ] ) ) $webDriver[ 'time' ][ 'max' ] = 0;

        $webDriver[ 'time' ][ 'min' ]  = min(
            $webDriver[ 'time' ][ 'min' ],
            array_sum( $times ) / count( $times ) );
        //$webDriver[ 'time' ][ 'max' ]  = max( $webDriver[ 'time' ][ 'max' ], $timeMax );

        unset( $times ); // Unset reference to avoid bugs
    }
    unset( $page ); // Unset reference to avoid bugs
}
unset( $webDriver ); // unset reference to avoid bugs



// mean
echo( "\nMean page load times in the 95% confidence zone:\n" );

// Tabs are used to make it easy to copy & paste the results from command line
// to a spreadsheet document. 8 character tabs are assumed.

// Results table header (2 lines)
printf( "%-46s", '' );
foreach( $webDrivers as $webDriver )
    printf( "\t%-15s\t%-15s", $webDriver[ 'browser' ], $webDriver[ 'browser' ] );
echo( "\n" );

printf( "%-46s", '' );
foreach( $webDrivers as $webDriver )
    printf( "\t%-15s\t%-15s", 'total', 'relative' );
echo( "\n" );



// Results table content
foreach( $pages as $key => $page )
{
    printf( "%-46s", $page[ 'url' ] );
    foreach( $webDrivers as $webDriver )
    {
        $browser = $webDriver[ 'browser' ];

        $times    = &$page[ 'time' ][ $browser ][ 'times' ];        // seconds (s)
        $mean     = array_sum( $times ) / count( $times ) * 1000.0; // milliseconds (ms)
        $min      = $webDriver[ 'time' ][ 'min' ]         * 1000.0; // milliseconds (ms)
        $relative = $mean - $min;

        printf( "\t%6.1f ms\t%+6.1f ms", $mean, $relative );

        unset( $times );
    }
    echo( "\n" );
}


// ====================================================================================
// Termination
// ====================================================================================

unset( $wd );

/*

Results for 50 loops/iterations ($loops = 50):


Mean page load times in the 95% confidence zone:
                                                chrome      chrome      firefox     firefox
                                                total       relative    total       relative
http://cdn.test.korolczuk.fr/from-host.html      710.6 ms    +17.1 ms    523.4 ms    +58.0 ms
http://test.korolczuk.fr/from-cdn.html           799.7 ms   +106.2 ms    520.8 ms    +55.3 ms
http://test.korolczuk.fr/from-host.html          693.5 ms     +0.0 ms    490.0 ms    +24.6 ms
http://test.korolczuk.fr/from-server.html        704.6 ms    +11.1 ms    465.4 ms     +0.0 ms



Mean page load times in the 95% confidence zone:
                                                chrome      chrome      firefox     firefox
                                                total       relative    total       relative
http://cdn.test.korolczuk.fr/from-host.html      908.5 ms   +246.0 ms   1094.7 ms   +628.4 ms
http://test.korolczuk.fr/from-cdn.html           774.8 ms   +112.2 ms    528.8 ms    +62.6 ms
http://test.korolczuk.fr/from-host.html          662.5 ms     +0.0 ms    493.1 ms    +26.9 ms
http://test.korolczuk.fr/from-server.html        662.9 ms     +0.4 ms    466.2 ms     +0.0 ms

*/
