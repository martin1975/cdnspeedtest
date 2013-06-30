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
  launch it (needed in the default code configuration; see `webdriver/index.php`, `$webDrivers` to modify default
  behavior).

- Get [Selenium WebDriver (aka Selenium)][http://docs.seleniumhq.org/docs/03_webdriver.jsp "Selenium"), install and
  launch it (optional; see `webdriver/index.php`, `$webDrivers` to modify default behavior).

- Get [any other WebDriver](https://code.google.com/p/selenium/w/list?q=label:WebDriver "Selenium WebDrivers"), install
  and launch it (optional; see `webdriver/index.php`, `$webDrivers` to modify default behavior).

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
        <th>Configuration Type<br />HTML / Resources</td>
        <th>Default web page</td>
        <th>Default Resources Host</td>
    </tr>

    <tr>
        <td>CDN / CDN</td>
        <td>http://cdn.test.korolczuk.fr/from-host.html</td>
        <td>http://cdn.test.korolczuk.fr/</td>
    </tr>

    <tr>
        <td>Server / CDN</td>
        <td>http://test.korolczuk.fr/from-cdn.html</td>
        <td>http://cdn.test.korolczuk.fr/</td>
    </tr>

    <tr>
        <td>Server / Server<br />(same host both)</td>
        <td>http://test.korolczuk.fr/from-host.html</td>
        <td>http://test.korolczuk.fr/</td>
    </tr>

    <tr>
        <td>Server / Server<br />(two different hosts)</td>
        <td>http://test.korolczuk.fr/from-server.html</td>
        <td>http://server.test.korolczuk.fr/</td>
    </tr>
</table>


Example result
--------------

An example of what you see in your browser:

<img width="320" height="293" src="data:image/png;base64,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" />

An example of the console output:

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

For any remark or comment, just drop a mail to cdnspeedtest@korolczuk.net.