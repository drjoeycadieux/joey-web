# Serverless Contact Form Setup

Choose your preferred serverless solution below:

## Option 1: Formspree (Recommended - Easiest)

**Best for:** Quick setup, no backend knowledge needed, free tier available

### Setup (2 minutes)

1. Go to [formspree.io](https://formspree.io)
2. Sign up for a free account
3. Click "Create" and select "Contact Form"
4. Enter your email
5. Copy your **Form ID** (looks like: `xyza1234`)
6. In `contact.html`, replace `YOUR_FORM_ID` with your actual ID:

```html
<form action="https://formspree.io/f/YOUR_FORM_ID" method="POST">
```

### Features
✅ No backend server needed  
✅ Free forever (up to 50 submissions/month)  
✅ Auto-replies to users  
✅ Email notifications  
✅ Spam filtering  

### How it works
- User submits form → Formspree receives data → You get email notification
- User sees "Thank you" page automatically
- Works with your existing HTML form

---

## Option 2: Vercel Functions

**Best for:** Deploying to Vercel, needs database storage

### Setup

1. Push code to GitHub
2. Connect to [vercel.com](https://vercel.com)
3. Create `api/contact.js`:

```javascript
export default async function handler(req, res) {
  if (req.method !== 'POST') {
    return res.status(405).json({ error: 'Method not allowed' });
  }

  const { name, email, subject, message } = req.body;

  // Send email via SendGrid, AWS SES, or Formspree
  // Store in database

  res.status(200).json({ success: true });
}
```

4. Update form action: `action="/api/contact"`

---

## Option 3: AWS Lambda + API Gateway

**Best for:** Scale and customization

### Setup

1. Create Lambda function in AWS Console
2. Use Node.js runtime
3. Add environment variables for DB credentials
4. Deploy with API Gateway trigger
5. Use endpoint URL as form action

---

## Option 4: Firebase Cloud Functions

**Best for:** Google ecosystem, real-time database

### Setup

1. Create Firebase project at [firebase.google.com](https://firebase.google.com)
2. Initialize with `firebase init functions`
3. Deploy function
4. Use function URL as form action

---

## Current Configuration

This contact form is configured for **Formspree** by default.

### Files
- `contact.html` - Form with Formspree action
- `assets/js/contact.js` - Basic form handling
- `server.js` - Optional Node.js backend (not needed with Formspree)

### To disable the Node.js server

Stop the running server and simply serve the static files:
```bash
# Option 1: Python
python3 -m http.server 8000

# Option 2: Node (http-server)
npx http-server

# Option 3: VS Code Live Server extension
```

---

## Form Data Flow (Formspree)

```
User submits form
    ↓
Form posts to Formspree API
    ↓
Formspree sends you an email
    ↓
User redirected to success page
```

No database, no backend code needed!

---

## Adding Database (Optional)

Even with Formspree, you can log submissions to your own database:

```javascript
// In contact.html, before form submission
fetch('/api/log-contact', {
  method: 'POST',
  body: JSON.stringify({ name, email, subject, message })
});
```

Then use the Node.js backend or serverless function to store in database.

---

## Troubleshooting

**Form not working:**
- Make sure you replaced `YOUR_FORM_ID` with actual Formspree ID
- Check browser console for errors
- Verify form method is `POST`

**Not receiving emails:**
- Check Formspree dashboard for submissions
- Verify email wasn't caught by spam filter
- Check Formspree account settings

**Want database storage:**
- Use Formspree + separate logging function
- Or use one of the other serverless options above
