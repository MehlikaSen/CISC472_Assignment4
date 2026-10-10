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