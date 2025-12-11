# Installing ngrok on Windows

## 🚀 Quick Install Methods

### Method 1: Using Chocolatey (Recommended)

If you have Chocolatey installed:

```powershell
# Run PowerShell as Administrator
choco install ngrok
```

### Method 2: Using Scoop

If you have Scoop installed:

```powershell
scoop install ngrok
```

### Method 3: Manual Installation (Easiest)

1. **Download ngrok:**
   - Visit: https://ngrok.com/download
   - Click "Download for Windows"
   - You'll get a file: `ngrok-v3-stable-windows-amd64.zip`

2. **Extract the file:**
   - Right-click the downloaded ZIP file
   - Select "Extract All..."
   - Extract to a folder like `C:\ngrok`

3. **Add to PATH (so you can run it from anywhere):**

   **Option A - Using System Settings:**
   - Press `Win + X` and select "System"
   - Click "Advanced system settings"
   - Click "Environment Variables"
   - Under "System variables", find "Path" and click "Edit"
   - Click "New" and add the path where you extracted ngrok (e.g., `C:\ngrok`)
   - Click OK on all windows
   - **Restart PowerShell**

   **Option B - Using PowerShell (Temporary for current session):**
   ```powershell
   $env:Path += ";C:\ngrok"
   ```

4. **Verify installation:**
   ```powershell
   ngrok version
   ```

### Method 4: Run ngrok Without Installing to PATH

If you don't want to modify PATH, you can run ngrok directly:

```powershell
# Navigate to where you extracted ngrok
cd C:\ngrok

# Run ngrok from there
.\ngrok http 8000
```

Or specify the full path:
```powershell
C:\ngrok\ngrok.exe http 8000
```

---

## 🔑 Sign Up and Configure (Optional but Recommended)

1. **Create free account:**
   - Visit: https://dashboard.ngrok.com/signup
   - Sign up for free account

2. **Get your auth token:**
   - After signing in, go to: https://dashboard.ngrok.com/get-started/your-authtoken
   - Copy your authtoken

3. **Add auth token to ngrok:**
   ```powershell
   ngrok config add-authtoken YOUR_AUTH_TOKEN_HERE
   ```

**Benefits of auth token:**
- Longer session times
- More connections
- Reserved domains (paid plans)
- Better reliability

---

## ✅ Quick Test

After installation, test ngrok:

```powershell
# Start ngrok
ngrok http 8000

# You should see:
# Forwarding   https://abc123.ngrok.io -> http://localhost:8000
```

**Keep this terminal open!** The ngrok tunnel will stay active as long as the command is running.

---

## 🔄 Alternative Solutions (No ngrok needed)

### Option 1: LocalTunnel (JavaScript-based)

```powershell
# Install LocalTunnel globally
npm install -g localtunnel

# Start tunnel
lt --port 8000

# You'll get: https://random-name.loca.lt
```

### Option 2: Cloudflare Tunnel (Free, No Signup)

1. Download cloudflared: https://developers.cloudflare.com/cloudflare-one/connections/connect-apps/install-and-setup/installation/
2. Run:
   ```powershell
   cloudflared tunnel --url http://localhost:8000
   ```

### Option 3: Deploy Directly to Production

Skip local testing and deploy directly to:
- Your VPS/server with HTTPS
- Laravel Forge
- Heroku
- DigitalOcean App Platform

---

## 🎯 Next Steps After Installing ngrok

1. Start Laravel:
   ```powershell
   php artisan serve
   ```

2. Start ngrok (in a new terminal):
   ```powershell
   ngrok http 8000
   ```

3. Copy the HTTPS URL from ngrok output (e.g., `https://abc123.ngrok.io`)

4. Configure WasenderAPI webhook:
   - Webhook URL: `https://abc123.ngrok.io/api/wasender/webhook`

5. Send a test message to verify!

---

## ⚠️ Important Notes

- **Free ngrok sessions** expire after 2 hours - you'll need to restart
- **URL changes** each time you restart ngrok (unless you have a paid plan)
- **Don't close** the ngrok terminal - the tunnel will stop
- **Restart PowerShell** after adding ngrok to PATH

---

## 🆘 Still Having Issues?

If ngrok installation fails, you can:

1. **Use the full path method** (no PATH modification needed):
   ```powershell
   C:\Users\Ali\Downloads\ngrok.exe http 8000
   ```

2. **Use LocalTunnel instead** (easier install):
   ```powershell
   npm install -g localtunnel
   lt --port 8000
   ```

3. **Deploy to production** and skip local webhook testing entirely

---

**Need help?** Check the PowerShell error message and try running PowerShell as Administrator.

