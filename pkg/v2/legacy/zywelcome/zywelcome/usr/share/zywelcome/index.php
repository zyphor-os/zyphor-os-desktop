<!DOCTYPE html>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Welcome to Zyphor OS</title>

<style>
    * {
        box-sizing: border-box;
    }

    body {
        margin: 0;
        min-height: 100vh;
        font-family: Inter, -apple-system, BlinkMacSystemFont,
                     "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
        background: #0a0a0a;
        color: #f5f5f5;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 30px;
    }

    .container {
        width: 100%;
        max-width: 800px;
        background: #111111;
        border: 1px solid #242424;
        border-radius: 14px;
        padding: 50px;
    }

    .logo {
        font-size: 28px;
        font-weight: 800;
        letter-spacing: -1px;
        margin-bottom: 8px;
    }

    .version {
        display: inline-block;
        font-size: 13px;
        color: #8f8f8f;
        margin-bottom: 35px;
    }

    h1 {
        font-size: 34px;
        letter-spacing: -1.5px;
        margin: 0 0 15px;
        font-weight: 750;
    }

    .intro {
        font-size: 16px;
        line-height: 1.8;
        color: #a1a1a1;
        margin-bottom: 35px;
    }

    .section {
        margin-top: 30px;
    }

    .section h2 {
        font-size: 18px;
        letter-spacing: -0.3px;
        margin-bottom: 10px;
        font-weight: 700;
    }

    .section p {
        color: #929292;
        line-height: 1.7;
        font-size: 15px;
    }

    .actions {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 40px;
    }

    .button {
        display: inline-block;
        text-decoration: none;
        padding: 11px 18px;
        border-radius: 7px;
        font-size: 14px;
        font-weight: 650;
        transition: all 0.15s ease;
    }

    .button.primary {
        background: #f5f5f5;
        color: #0a0a0a;
    }

    .button.primary:hover {
        background: #dcdcdc;
    }

    .button.secondary {
        background: transparent;
        color: #d4d4d4;
        border: 1px solid #303030;
    }

    .button.secondary:hover {
        background: #1a1a1a;
        border-color: #444444;
    }

    .footer {
        margin-top: 45px;
        padding-top: 20px;
        border-top: 1px solid #242424;
        font-size: 12px;
        line-height: 1.7;
        color: #666666;
    }

    @media (max-width: 600px) {
        body {
            padding: 15px;
        }

        .container {
            padding: 30px;
        }

        .logo {
            font-size: 24px;
        }

        h1 {
            font-size: 28px;
        }
    }
</style>

</head>

<body>

<main class="container">

    <div class="logo">Zyphor OS</div>

    <span class="version">
        Welcome to your new operating system
    </span>

    <h1>Welcome to Zyphor OS!</h1>

    <p class="intro">
        Thank you for choosing Zyphor OS. Your system is now ready.
        Zyphor OS is designed to provide a clean, controlled, and
        user-friendly Linux experience—giving you the tools you need
        while keeping the system simple and focused.
    </p>

    <div class="section">
        <h2>What's next?</h2>

        <p>
            Take a moment to explore your new system. You can manage
            applications, configure your desktop, learn about Zyphor OS,
            and discover the tools available to help you get started.
        </p>
    </div>

    <div class="section">
        <h2>Explore Zyphor OS</h2>

        <p>
            Learn more about Zyphor OS, read the documentation, and
            discover the features and technologies that make the system
            possible.
        </p>
    </div>

    <div class="actions">

        <a
            class="button primary"
            href="https://zyphor-os.github.io"
        >
            Visit Zyphor OS
        </a>

        <a
            class="button secondary"
            href="https://zyphor-os.github.io/docs.html"
        >
            View Documentation
        </a>

    </div>

    <div class="footer">
        Built with Linux. Designed for simplicity. Powered by the Zyphor community.
        <br><br>
        Welcome to Zyphor OS.
    </div>

</main>

</body>
</html>
