CDN Speed Test
==============

CDN Speed Test is aimed to test the performance of the [OVH CDN](https://www.ovh.com/fr/cdn/ "OVH Content Delivery
Network") in terms of delivery speed as compared to the same content (or a similar one) loaded without the help of the
CDN.

Installing
----------

- Get the project.

- Get [facebook/php-driver](https://github.com/facebook/php-webdriver "php-webdriver") and copy it in
  `webdriver/php-webdriver`

- Update the contents of `www/css/style-*.css` files to point to your server (default are `*.test.korolczuk.fr`).

- Update the contents of `www/img/from-cdn.html` and /www/img/from-server.html to point to your server.

- Configure your CDN accordingly to your settings (including the OVH CDN).

- Configure the Apache web server accordingly to your settings (or any other web server with a configuration
  equivalent to the one in www/.htaccess).

- Configure your DNS accordingly to you settings. Wait for propagation (depends of your DNS configuration, often
  up to 24 or 48 hours).

- Get [Chrome WebDriver (aka ChromeDriver)](https://code.google.com/p/chromedriver/ "Chrome WebDriver"), install and
  launch it (needed in the default code configuration, see `webdriver/index.php`, `$webDrivers` to modify default
  behavior).

- Get [Selenium WebDriver (aka Selenium)][http://docs.seleniumhq.org/docs/03_webdriver.jsp "Selenium"), install and
  launch it (optional, see `webdriver/index.php`, `$webDrivers` to modify default behavior).

- Get [any other WebDriver](https://code.google.com/p/selenium/w/list?q=label:WebDriver "Selenium WebDrivers"), install
  and launch it (optional, see `webdriver/index.php`, `$webDrivers` to modify default behavior).

- Launch each test at least (using your favorite browser or running this performance suite) in order to force the CDN
  data upload.

Principle
---------

The principle is to load the same content in miscellaneous CDN vs. dedicated server configurations. Page loading is made
using standard browsers using the WebDriver automation framework. Several tests are performed in each test run in random
order. All the timings are displayed. Finally, we remove all timings outside a 95% confidence zone before displaying
mean values for each page and browser.

Run
---

    php webdriver/index.php

Details
-------

Each test page is made of one HTML content and a set of CSS stylesheets and PNG images.

There are 3 different web pages:

- `from-host.html`: the HTML content as well as resources are loaded from the current host (server or CDN);

- `from-cdn.html`: the HTML content is intended to be loaded from the dedicated server, while all the resources are
  loaded from the CDN;

- `from-server.html`: the HTML content is intended to be loaded from the dedicated server, while all the resources are
  loaded from the CDN.

There are several resources:

- `css/pure-min.css` is Yahoo! Pure reset stylesheet;
- `css/style-*.css` are page-specific stylesheets, one per host configuration;
- `img/?.png` are various images;
- `img/bckgrnd.png` is a background image referenced by the page-specific stylesheet.

There are 4 test configurations (here with default values):

<table>
    <tr>
            <td>Default web page</td>
            <td>Default Resources Host</td>
    </tr>

    <tr>
        <th>
            <td>http://cdn.test.korolczuk.fr/from-host.html</td>
            <td>http://cdn.test.korolczuk.fr/</td>
        </th>
    </tr>

    <tr>
        <th>
            <td>http://cdn.test.korolczuk.fr/from-cdn.html</td>
            <td>http://cdn.test.korolczuk.fr/</td>
        </th>
    </tr>

    <tr>
            <td>http://test.korolczuk.fr/from-host.html</td>
            <td>http://test.korolczuk.fr/</td>
    </tr>

    <tr>
            <td>http://test.korolczuk.fr/from-server.html</td>
            <td>http://server.test.korolczuk.fr/</td>
    </tr>
</table>


Example result
--------------

Mean page load times in the 95% confidence zone:

                                                    chrome      chrome      firefox     firefox
                                                    total       relative    total       relative
    http://cdn.test.korolczuk.fr/from-host.html      908.5 ms   +246.0 ms   1094.7 ms   +628.4 ms
    http://test.korolczuk.fr/from-cdn.html           774.8 ms   +112.2 ms    528.8 ms    +62.6 ms
    http://test.korolczuk.fr/from-host.html          662.5 ms     +0.0 ms    493.1 ms    +26.9 ms
    http://test.korolczuk.fr/from-server.html        662.9 ms     +0.4 ms    466.2 ms     +0.0 ms


Remarks
-------

Please notice WebDriver has an important overhead. However, this does not affect the relative difference between
results, when performed manually using the developer tools included in browsers.


Contact
-------

For any remark or comment, just drop an email to cdnspeedtest@korolczuk.net.