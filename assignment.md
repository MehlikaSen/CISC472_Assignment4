**Hosted application:** [https://cisc472-assignment4.onrender.com](https://cisc472-assignment4.onrender.com)

# Predictable password-reset token

**Location:** `src/crypto.php`, in `reset_token_for()`
The reset token is created from the user's email address and the current date using `sha1()`.

**Trigger:**

1. Open the hosted application and go to 'Forgot password'
2. Enter an existing account email, such as `maya@campus.edu`.
3. Calculate the token using `SHA1("maya@campus.edu|2026-10-09")`.
4. Open the password reset form and submit the email, calculated token, and a new password.
5. The password changes even though the reset email was never received.

*Example:* A user wants to reset `maya@campus.edu` on `2026-10-09`. The app creates the token from `SHA1("maya@campus.edu|2026-10-09")` since the email and date are not secret, another person can calculate the same token and change thepassword without accessing her email.

**The Fix**: Generate a random token with a secure function such as `random_bytes()`. Store the token, the account email, and an expiration time in the database. Put the token in the reset link sent to the user. When the user submits the form, compare it with the stored token, reject expired tokens, and delete the token after it is used. This makes the token difficult to guess and usable only once.

**how a password becomes the value stored in SQLite**:
When a user registers or changes a password, the app sends the password to hash_password(). That function converts it into a SHA-256 hash, and only the hash is stored in the `password_hash` column in SQLite. During sign-in, the entered password is hashed again and compared with the stored hash. The original password is not stored.

**what happens after someone clicks 'Forgot password'**:
The user enters an email address, and the app checks whether the account exists. It then creates a reset token from the email and current date, stores the token in SQLite, and displays a message saying that a reset link was queued. The token is predictable because it is based on information that is not secret, which is the security flaw.

**how vault rows are scoped to an account**:
Each vault row has a `user_id` that connects it to a specific account. After login, the app gets the signed-in user's ID and queries only rows belonging to that ID. This prevents one member from seeing another member's stored secrets through the normal vault page.

**who the advisor account is for, even if you cannot sign in as them yet**:
The advisor account belongs to the student organization's club advisor. It is intended for organization-wide or emergency information, such as the emergency disbursement PIN. The account is created in the seed data, but its password is not provided in the README, so users cannot normally sign in as the advisor.