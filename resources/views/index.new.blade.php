<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
    <meta charset="utf-8">
    <title>TimeEditEdit</title>
    <meta name="Description" content="A proxy for intercepting the ugly TimeEdit Schedule and making it readable">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        * {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            height: 100%;
        }

        body {
            /* The image used */
            background-image: url("/img/bg.jpg");

            /* Center and scale the image nicely */
            background-position: center;
            background-repeat: no-repeat;
            background-size: cover;

            /* Position the content correctly in the middle */
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;

            font-family: 'Open Sans', sans-serif;
        }

        main {
            background-color: white;
            padding: 10px 20px;
            margin: 5px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        h1 {
            margin: 5px 0;
        }

        h2 {
            font-size: 14pt;
            margin: 0;
        }

        .link-container {
            display: none;
            flex-direction: column;
            align-items: center;
            width: 100%;
        }

        .link-container.has-text {
            display: flex;
        }

        .link-container .inner {
            display: flex;
            align-items: center;
        }

        .text-input {
            display: block;
            width: 100%;
            height: 34px;
            padding: 6px 12px;
            font-size: 17px;
            color: #555;
            background-color: #fff;
            border: 1px solid #ccc;
            border-radius: 4px;

            margin: 0;
        }

        .link-container .text-input {
            margin: 10px 0;
            border-radius: 4px 0px 0px 4px;
        }

        .btn-copy {
            background-color: white;
            border: 1px solid #ccc;
            box-shadow: none;
            border-radius: 0px 4px 4px 0px;
            border-left: 0px;
        }

        .link-container .btn-copy {
            height: 34px;
        }

        /**
         * Custom checkbox start
         */
        .checkbox-container {
            display: block;
            position: relative;
            padding-right: 25px;
            margin-bottom: 10px;
            cursor: pointer;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }

        /* Hide the browser's default checkbox */
        .checkbox-container input {
            position: absolute;
            opacity: 0;
            cursor: pointer;
            pointer-events: none;
        }

        /* Create a custom checkbox */
        .checkmark {
            position: absolute;
            top: 0;
            right: 0;
            height: 20px;
            width: 20px;
            background-color: #eee;
            border: 1px solid #ccc;
            z-index: 1;
        }

        /* On mouse-over, add a grey background color */
        .checkbox-container:hover input ~ .checkmark {
            background-color: #ccc;
        }

        /* When the checkbox is checked, add a blue background */
        .checkbox-container input:checked ~ .checkmark {
            background-color: #2196F3;
        }

        /* Create the checkmark/indicator (hidden when not checked) */
        .checkmark:after {
            content: "";
            position: absolute;
            display: none;
        }

        /* Show the checkmark when checked */
        .checkbox-container input:checked ~ .checkmark:after {
            display: block;
        }

        /* Style the checkmark/indicator */
        .checkbox-container .checkmark:after {
            left: 6px;
            top: 2.5px;
            width: 4px;
            height: 8px;
            border: solid white;
            border-width: 0 3px 3px 0;
            transform: rotate(45deg);
        }
        /**
         * Custom checkbox End
         */

        #popup-trigger {
            align-self: flex-start;
            font-size: 9pt;
            margin-bottom: 10px;
        }

        footer {
            padding: 20px 5px 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            font-size: 10pt;
            text-align: center;
        }

        footer > * {
            margin: 0;
            padding: 0;
        }

        footer .legal {
            color: #444;
            font-size: 8pt;
        }

        .popup {
            position: fixed;
            background-color: rgba(0,0,0,0.7);
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            padding: 0 5px;

            z-index: 999;

            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;

            pointer-events: none;
            opacity: 0;

            transition: opacity 200ms linear;
        }

        .popup.open {
            opacity: 1;
            pointer-events: all;
        }

        .popup .popup-container{
            background-color: white;
            border-radius: 15px;
            padding: 35px 5px 10px 15px;
            margin: 0 5px;
            width: 100%;
            max-width: 700px;
            max-height: 80%;
            overflow-y: hidden;

            position: relative;

            transform: translateY(-100%);
            opacity: 0;

            transition: transform 500ms ease-in-out, opacity 200ms linear;
        }

        .popup.open .popup-container {
            transform: translateY(0%);
            opacity: 1;
        }

        .popup .btn-close {
            background: none;
            border: 0px;
            box-shadow: none;
            position: absolute;
            top: 5px;
            right: 5px;
            font-size: 24pt;
        }

        .popup-content {
            overflow-y: auto;
            overflow-wrap: break-word;
            max-height: 600px;
        }

        .popup-content p img {
            height: 25px;
        }
    </style>

    <!-- Lazyload assets in browsers that has javascript, and revert back to normal loading when they dont -->
    <!-- Link: https://dassur.ma/things/lazyloading/ -->
    <noscript class="lazyload">
        <link target="_blank" href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet">
    </noscript>
</head>

<body>

    <aside class="popup" id="howto-popup">
        <article class="popup-container">
            <button class="btn-close">&times;</button>
            <div class="popup-content">
                <h1>How do I use this site?</h1>
                <p>To use this service you need to obtain the id of a valid schedule on TimeEdit.</p>
                <p>This can easily be done though the TimeEdit website, once you have configured/have access to your schedule.</p>
                <p>On the page for your chosen schedule you need to obtain the subscribtion link by pressing the <img src="/img/instructions-subscribe.png" alt="Subscribtion Button"> button and make a copy of the link that is now shown.</p>
                <p>The link should be in the following format: https://cloud.timeedit.net/itu/web/public/<b>abc123abc123</b>.ics</p>
                <p>The id that should be provided to this service is the text between <i>public/</i> and <i>.ics</i> (<b>abc123abc123</b> in the example above)</p>
                <p>Now that the TimeEdit id is obtain, you simply need to paste the link in the textbox on this site. You will now immedially be presented with a new link that can be pasted directly into your calendar program of choise.</p>

                <b>Enjoy your new shiny schedule!</b>
                <br><br>


                <h1>How does it work?</h1>
                <p>TimeEditEdit acts as a proxy (middleman) between your calendar program (such as Google calendar and outlook) and TimeEdit.</p>
                <p>This means that when your calendar program tries to fetch new updates to the schedule, it asks our service instead of TimeEdit directly.</p>
                <p>Our service then downloads the schedule from TimeEdit, performs the transformations and proper formatting and then sends it to your calendar program.</p>

                <p>In more technical terms the TimeEdit schedule is distribued as an ICS file (which is the de-facto standard file type for distributing and sharing calendar events).</p>
                <p>By parsing this file we can extract the important information, perform sensible modifications (such as translations) and afterwards generate a new ICS file that is send to your calendar program.</p>
            </div>
        </article>
    </aside>

    <main>
        <h1>TimeEditEdit</h1>

        <div class="link-container">
            <label for="input">Here is your new improved link:</label>
            <div class="inner">
                <input id="link-dest" type="text" class="text-input" readonly>
                <button id="copy-btn" class="btn-copy">&#9986;</button>
            </div>
        </div>

        <label for="input">Enter TimeEdit Id Here:</label>
        <input class="text-input" type="text" id="input" placeholder="Enter your timeedit id here" aria-label="Enter your timeedit id here">
        <a id="popup-trigger" href="#">How do I use this site?</a>

        <h2>Options:</h2>

        <label for="plaintext_checkbox" class="checkbox-container">
            Plaintext-mode:
            <input id="plaintext_checkbox" type="checkbox">
            <span class="checkmark"></span>
        </label>

        <label for="lang_select">Language:</label>
        <select id="lang_select">
            <option value="da">Danish</option>
            <option value="en">English</option>
        </select>

        <footer>
            <p>This project is open source!<br>Check us out on <a target="_blank" rel="noopener" href="https://github.com/jlndk/TimeEditEdit">https://github.com/jlndk/TimeEditEdit</a></p>
            <br>
            <p class="legal">Created by Jonas Lindenskov Nielsen (<a target="_blank" rel="noopener" href="https://jlndk.me">https://jlndk.me</a>).<br> This project is neither assosiated with TimeEdit nor The IT University of Copenhagen.<br> This project is licensed under these <a target="_blank" rel="noopener" href="https://github.com/jlndk/TimeEditEdit/blob/master/LICENSE.md">Terms and conditions</a></p>
        </footer>
    </main>
    <script src="js/main.js"></script>
</body>

</html>