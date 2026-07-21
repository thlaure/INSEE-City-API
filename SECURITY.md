# Security Policy

## Supported Versions

This project currently supports the latest version available on the `main` branch.

| Version | Supported |
|---|---|
| Latest `main` | Yes |
| Older commits or forks | No |

Security fixes are applied to `main`. No backports are currently provided.

## Reporting a Vulnerability

Please do not report security vulnerabilities through public GitHub issues, discussions, pull requests, or social media.

Report vulnerabilities privately through [GitHub Security Advisories](https://github.com/thlaure/world-cities-api/security/advisories/new).

Include as much of the following information as possible:

- A clear description of the vulnerability.
- The affected endpoint, component, file, or configuration.
- Steps required to reproduce the issue.
- A minimal proof of concept, if available.
- The potential impact.
- Any relevant logs or HTTP requests with secrets and personal data removed.
- A suggested remediation, if known.

Do not include API keys, passwords, private keys, personal data, or unrelated confidential information.

## Response Process

The maintainers will make a reasonable effort to:

1. Acknowledge the report within three business days.
2. Confirm whether the vulnerability can be reproduced.
3. Assess its severity and affected versions.
4. Develop and verify a remediation.
5. Coordinate disclosure with the reporter.
6. Publish a security advisory when appropriate.

Timelines may vary depending on complexity and maintainer availability. Reporters will be informed of material progress when possible.

## Scope

Security reports may concern:

- API input validation or output exposure.
- Authentication or authorization behavior, if introduced.
- SQL, command, header, log, or other injection vulnerabilities.
- Server-side request forgery or unsafe outbound HTTP requests.
- Rate-limit bypasses with practical security impact.
- Exposure of secrets, credentials, internal errors, or sensitive data.
- Unsafe city-data import behavior.
- GeoNames, Geo API, or Photon integration vulnerabilities.
- PostgreSQL persistence vulnerabilities.
- Symfony, API Platform, FrankenPHP, Docker, or production configuration issues.
- Vulnerable dependencies with a demonstrated impact on this project.
- CI/CD weaknesses that could compromise source code, releases, or deployed environments.

## Out of Scope

The following are generally not considered security vulnerabilities:

- Missing security headers without a demonstrated impact.
- Automated scanner reports without validation or a reproducible attack path.
- Dependency findings that do not affect the project’s installed version or usage.
- Denial-of-service reports requiring unrealistic traffic volumes.
- Reports involving unsupported forks or modified deployments.
- Social engineering, phishing, or physical attacks.
- Vulnerabilities that require prior compromise of the host or maintainer account.
- Publicly known issues for which an upstream fix is not yet available, unless this project can reasonably mitigate them.
- Findings based only on development defaults that are explicitly replaced or required in production configuration.
- Disclosure of public city or address-search data intentionally exposed by the API.

## Testing Guidelines

Security research must:

- Use systems and data you own or are explicitly authorized to test.
- Avoid accessing, modifying, or deleting other users’ data.
- Avoid disrupting project infrastructure or third-party services.
- Avoid denial-of-service testing.
- Avoid automated high-volume scanning.
- Stop testing and report immediately if sensitive information is encountered.
- Comply with applicable laws and third-party service terms.

Do not test the public GeoNames, Geo API, Photon, GitHub, or other third-party infrastructure as part of a report against this project without their explicit authorization.

## Disclosure

Please allow the maintainers reasonable time to investigate and remediate a vulnerability before public disclosure.

The maintainers will aim to credit reporters in the security advisory unless they request anonymity. Submission of a report does not guarantee a bounty or other compensation.

## Security Updates

Security fixes and advisories are published through the repository’s [Security Advisories](https://github.com/thlaure/world-cities-api/security/advisories) and release history when applicable.
