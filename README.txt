================================================================
  Akkshay Sharma — Static site for Hostinger (basic plan friendly)
================================================================

WHY THIS CHANGED
----------------
Your original project was a Next.js (Node.js) app. Hostinger's basic /
shared plans only run static files + PHP — they do NOT run Node.js.
This bundle is a 1-page rebuild of the same site that needs zero Node.js
and works on every Hostinger plan.

Tech stack now:
  * index.html ............ pure HTML, no build step
  * Tailwind CSS .......... loaded from CDN (no compile required)
  * Lucide icons .......... loaded from CDN
  * Google Fonts .......... loaded from CDN
  * submit.php ............ form handler (replaces Supabase server actions)

WHAT'S IN THIS FOLDER
---------------------
  index.html              Full site — hero, stats, about, experience,
                          skills, testimonials, mentorship, contact tabs.
  submit.php              Handles the three forms (Book Session, Send
                          Message, Subscribe).  Saves entries to /data
                          and emails them to you via PHP mail().
  images/
    akkshay-sharma.png    Your hero photo (carried over from the Next.js
                          /public folder).
  icon.svg                Favicon (vector).
  icon-light-32x32.png    Light-mode favicon.
  icon-dark-32x32.png     Dark-mode favicon.
  apple-icon.png          Apple touch icon.
  README.txt              This file.

HOW TO DEPLOY TO HOSTINGER (5 minutes)
--------------------------------------
1.  Log in to your Hostinger control panel (hPanel).
2.  Open  Websites -> your domain -> File Manager.
3.  Navigate into  /public_html/   (delete the default "default.php"
    or "index.html" placeholder file that Hostinger drops in there).
4.  Upload everything in this folder INTO /public_html/ — so the final
    structure on the server looks like:

        public_html/
        ├── index.html
        ├── submit.php
        ├── icon.svg
        ├── icon-light-32x32.png
        ├── icon-dark-32x32.png
        ├── apple-icon.png
        ├── README.txt
        └── images/akkshay-sharma.png

    (You can drag the whole .zip into File Manager and use "Extract".)

5.  IMPORTANT — open submit.php in File Manager, click "Edit", and
    change this line at the top:

        $RECIPIENT_EMAIL = 'connect@akkshaysharma.com';

    to whichever inbox you want submissions delivered to.

6.  Visit your domain in the browser. Done.

TESTING THE FORMS
-----------------
* Submit the "Send Message" form — you should see a green success
  message under the button and receive an email at $RECIPIENT_EMAIL.
* On the server, every submission is also appended to
  /public_html/data/contact.csv (or booking.csv / newsletter.csv).
  This folder is auto-created and protected with a .htaccess that
  blocks public access. You can download the CSVs any time from
  File Manager.

IF EMAILS DON'T ARRIVE
----------------------
Some shared plans have PHP mail() throttled or routed through an
external SMTP relay. Two easy fallbacks:

  Option A — Use Hostinger's "Email Forwarders" so mail() goes to your
             real inbox. Set this up in hPanel -> Emails.

  Option B — Switch the form handler to a free hosted service:
             - Formspree (https://formspree.io)  — change each <form>'s
               action to your Formspree endpoint and remove submit.php.
             - Web3Forms (https://web3forms.com) — same idea, free, no
               account-confirm required.

CSV submissions still work even if mail() fails.

NO-PHP OPTION (purely static)
-----------------------------
If you don't want PHP at all (e.g. you put the site on Hostinger's free
preview, GitHub Pages, Netlify, etc.), do this:

  1. Delete submit.php.
  2. In index.html, change every  action="submit.php"  to a Formspree
     or Web3Forms URL.

The site will still look and behave the same — only the backend changes.

CUSTOMIZING
-----------
* Calendly / Cal.com link:  search index.html for
      cal.com/akkshay-sharma/15min
  and replace with whatever scheduling link you prefer.
* LinkedIn URL:  search for  linkedin.com/in/akkshaysharma
* Email link:    search for  mailto:connect@akkshaysharma.com
* Text content / experience / testimonials are plain HTML — just edit
  them in place.
* Colours: see the  tailwind.config = { ... }  block near the top of
  index.html — change `primary`, `background`, `foreground`, etc.

That's it. Drop it in /public_html and you're live.
