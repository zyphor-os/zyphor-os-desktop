const releaseNotes = {
    distro: "Zyphor OS 3 \"Bethany\" LTS",
    version: "3.0.0-bethany-lts-u1",
    date: "Updated On: September 26, 2026 @ 08:14 AM",

    sections: [
        {
            "title": "Zyphor Web Server Setup",
            "items": [
                "Added zyphor setup web-server for spinning up an Apache virtual host in one command",
                "Automatically enables the rewrite and ssl Apache modules if they aren't already active",
                "Generates a self-signed SSL certificate on first run and skips regeneration if one is already configured",
                "Prompts for a port and adds it to ports.conf, skipping port 443 since it's already listened on by default",
                "Prompts for a site name to create and enable a ready-to-use VirtualHost config with SSL, logging, and directory permissions pre-set",
                "Creates a default index.html in the site's DocumentRoot so the new site has a working landing page out of the box",
                "Restarts Apache automatically once the site is enabled"
            ]
        },
    ]
};