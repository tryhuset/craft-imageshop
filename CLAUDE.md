# CLAUDE.md - Development Notes

## Local Development Environment

This project runs locally using **DDEV** (Docker containers for PHP development).

### Setup

- Development environment is managed by DDEV
- Start the environment: `ddev start`
- Stop the environment: `ddev stop`

### Craft CMS Version

- This plugin is for **Craft CMS 5**
- Requires PHP 8.0.2+ or PHP 9.0+

### Key Directories

- Plugin code: `/src/`
- Vue components: `/src/vue/`
- Templates: `/src/templates/`
- Services: `/src/services/`
- Controllers: `/src/controllers/`

### Common Commands

- Install dependencies: `composer install` (usually done in DDEV container)
- Build Vue components: `cd src/vue && npm run build`

### Resources

- Repository: https://git1.apt.no/open/craft-imageshop
- Imageshop Integration: https://www.imageshop.org/
