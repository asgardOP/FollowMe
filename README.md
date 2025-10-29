# FollowMe – Photo Sharing Social Platform

**FollowMe** is a lightweight social media web app built with **PHP** and **MySQL**.  
It focuses on the essential features of a social network: user accounts, content posting, and follower relationships — all in a clean, minimal setup.

---

🎥 [Watch the demo on Streamable](https://streamable.com/0b7zew]

--

## Features

### 👤 User Registration & Authentication
- Users can sign up with a **unique username** and optional **profile picture**.
- Sessions are handled securely using **HTTP cookies**.
- Usernames are validated using regular expressions to ensure format consistency.

### 🖼️ Post Creation
- Upload **images** (`.jpg`, `.png`, etc.) with a **text caption**.
- Uploaded files are validated and stored safely.
- Posts are stored in the database with metadata (caption, user ID, timestamp).

### 🏠 Personalized Feed
- Displays posts from the logged-in user and accounts they follow.
- Ordered chronologically for a clean and intuitive user experience.

### 🔍 Explore Page
- Browse recent posts from **all other users**.
- Encourages discovery of new profiles and content.

### ➕ Follow / Unfollow System
- Follow users to see their posts in your feed.
- Unfollow to remove them — instant update to your content view.

### 👥 User Profiles
- Each profile includes:
  - Profile picture, display name, username
  - Join date, followers, following counts
  - User’s personal photo grid (posts)

---

## 🔒 Security Highlights

| Area | Implementation |
|------|----------------|
| **SQL Injection** | Uses `mysqli_prepare()` and `mysqli_stmt_bind_param()` for all queries |
| **XSS Protection** | Sanitizes output with `htmlspecialchars()` |
| **File Upload Security** | Renames uploaded files using `time() . "_" . bin2hex(random_bytes(6))` to avoid collisions and path traversal |
| **Session Handling** | Uses secure cookie-based sessions for login management |

---

## 🗄️ Tech Stack
- **Frontend:** HTML5, CSS3
- **Backend:** PHP (Procedural)
- **Database:** MySQL
- **Server:** XAMPP / Apache (localhost or remote)

---

## Author
  Ali Emad (asgard)
