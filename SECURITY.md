# Security Policy

## Supported Versions

| Version | Supported          |
| ------- | ------------------ |
| 1.x     | ✅ Fully supported |

## Reporting a Vulnerability

If you discover a security vulnerability in Grubeli.ge, please report it privately. **Do not disclose it publicly until we have had a chance to address it.**

### How to Report

- **Email**: ormocidzee@gmail.com
- **Subject**: "[SECURITY] Vulnerability in Grubeli.ge"
- **Include**:
  - Description of the vulnerability
  - Steps to reproduce
  - Potential impact
  - Suggested fix (if any)

### What to Expect

1. **Acknowledgment** within 48 hours
2. **Initial assessment** within 5 business days
3. **Fix timeline** — we will prioritize based on severity:
   - 🔴 **Critical**: 24-48 hours
   - 🟡 **High**: within 7 days
   - 🟢 **Medium**: within 14 days
   - ⚪ **Low**: next release cycle

---

## Current Security Status

### ✅ Resolved
- SSL verification enabled for all external API calls
- Nominatim reverse geocode cached (24h TTL) — rate limit compliant
- User-Agent strings include contact email

### 🚧 In Progress
- Session timeout configuration
- CSRF tokens for POST endpoints
- Rate limiting on location save endpoint

### 📋 Known Non-Issues
- Cache directory is outside webroot (`../cache/`)
- Session data does not contain PII beyond location preference
- No user accounts or authentication required

---

## Security Best Practices for Contributors

### API Keys
- Never commit API keys to the repository
- Use `.env` file for local development (see `.env.example`)
- NASA API key is optional — fire monitoring works without it

### Code Reviews
- All PRs require at least one review
- Security-sensitive changes (auth, API calls, crypto) need explicit approval
- Run `git diff` to check for accidentally committed secrets

### Testing
- Test with PHP's built-in server: `php -S localhost:8000`
- Verify SSL/TLS connections with `curl -vI https://api.open-meteo.com`
- Check error logs: `tail -f logs/error.log`