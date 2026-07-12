# Company Email Setup

## Required Outcome

- Public address: `contact@infinitesugar.com`
- Forward destination: `redfemmes@gmail.com`
- Messages sent to `contact@infinitesugar.com` should arrive in Gmail.
- Replies sent from Gmail should appear to customers as `contact@infinitesugar.com`.

Do not commit mailbox passwords, SMTP passwords, API keys, or DNS provider credentials.

## Recommended Mail Provider

Use either the hosting control panel's mailbox service or a dedicated provider such as Google Workspace, Zoho Mail, Fastmail, or your DNS/hosting provider's managed email service.

For the simplest forwarding-plus-reply setup, create a real mailbox for `contact@infinitesugar.com`, then add forwarding to `redfemmes@gmail.com`. A real mailbox is preferred over forward-only aliases because Gmail's **Send mail as** flow needs authenticated SMTP credentials.

## DNS Records

Add the exact records supplied by the selected mail provider:

- MX: routes inbound mail for `infinitesugar.com` to the provider.
- SPF: authorizes the provider to send mail for the domain. Usually a TXT record similar to `v=spf1 include:provider.example ~all`.
- DKIM: cryptographic signing record, usually a TXT record with a selector such as `selector1._domainkey`.
- DMARC: policy and reporting record, usually a TXT record at `_dmarc.infinitesugar.com`.

Start DMARC in monitoring mode if deliverability is uncertain:

```text
v=DMARC1; p=none; rua=mailto:contact@infinitesugar.com; adkim=s; aspf=s
```

After SPF/DKIM are verified and replies are working, the policy can be tightened to `quarantine` or `reject`.

## Control Panel Steps

If the server or hosting panel supports mailbox creation:

1. Open the domain email/mailbox section for `infinitesugar.com`.
2. Create the mailbox `contact@infinitesugar.com`.
3. Generate a strong mailbox password and store it only in the approved password manager.
4. Enable forwarding from `contact@infinitesugar.com` to `redfemmes@gmail.com`.
5. Copy the provider's SMTP host, port, and security mode.
6. Add or verify the provider's MX, SPF, DKIM, and DMARC DNS records.
7. Send a test message from an outside account to `contact@infinitesugar.com`.
8. Confirm the message arrives at `redfemmes@gmail.com`.

Common SMTP settings to record separately:

- SMTP host: provided by the mail provider or control panel.
- Port: `587` with STARTTLS, or `465` with SSL/TLS.
- Username: `contact@infinitesugar.com`.
- Password: mailbox/app password, never committed to Git.

## Gmail Send Mail As

1. In Gmail, open Settings.
2. Go to **Accounts and Import**.
3. Under **Send mail as**, click **Add another email address**.
4. Enter `contact@infinitesugar.com`.
5. Choose **Send through SMTP server**.
6. Enter the provider's SMTP host, port, username, password, and TLS/SSL option.
7. Complete Gmail's verification email.
8. Set `contact@infinitesugar.com` as the default sending identity if desired.
9. Send a reply test and confirm the recipient sees `contact@infinitesugar.com`.
