// Theme Switcher for ISP Management System
class ThemeSwitcher {
    constructor() {
        this.currentTheme = localStorage.getItem('isp-theme') || 'default';
        this.currentFont = localStorage.getItem('isp-font') || 'Roboto';
        this.currentSize = localStorage.getItem('isp-size') || 'normal';
        this.customColors = JSON.parse(localStorage.getItem('isp-custom-colors') || '{}');
        this.themeConfig = null;
        this.init();
    }

    async init() {
        try {
            // Load theme configuration
            const response = await fetch('../ui/styles/themes/theme-config.json');
            this.themeConfig = await response.json();
            
            // Apply saved theme or default
            this.applyTheme(this.currentTheme);
            this.applyFont(this.currentFont);
            this.applySize(this.currentSize);
            
            // Create theme switcher UI
            this.createThemeSwitcher();
        } catch (error) {
            console.error('Error initializing theme switcher:', error);
        }
    }

    createThemeSwitcher() {
        // Create theme switcher container
        const container = document.createElement('div');
        container.className = 'theme-switcher';
        container.innerHTML = `
            <div class="theme-switcher-toggle">
                <i class="fas fa-palette"></i>
            </div>
            <div class="theme-switcher-menu">
                <div class="theme-tabs">
                    <button class="active" data-tab="themes">Themes</button>
                    <button data-tab="customize">Customize</button>
                </div>
                <div class="tab-content">
                    <div class="tab-pane active" id="themes">
                        <h6>Select Theme</h6>
                        <div class="theme-options"></div>
                    </div>
                    <div class="tab-pane" id="customize">
                        <h6>Customize Theme</h6>
                        <div class="customize-section">
                            <label>Font Family</label>
                            <select class="font-selector">
                                ${this.themeConfig.font_options.map(font => 
                                    `<option value="${font}" ${this.currentFont === font ? 'selected' : ''}>${font}</option>`
                                ).join('')}
                            </select>
                        </div>
                        <div class="customize-section">
                            <label>Text Size</label>
                            <select class="size-selector">
                                ${Object.entries(this.themeConfig.size_options).map(([size, value]) => 
                                    `<option value="${size}" ${this.currentSize === size ? 'selected' : ''}>${size.charAt(0).toUpperCase() + size.slice(1)}</option>`
                                ).join('')}
                            </select>
                        </div>
                        <div class="customize-section colors-section">
                            <label>Colors</label>
                            <div class="color-pickers"></div>
                        </div>
                    </div>
                </div>
            </div>
        `;

        // Add theme options
        const optionsContainer = container.querySelector('.theme-options');
        this.themeConfig.available_themes.forEach(theme => {
            const option = document.createElement('div');
            option.className = `theme-option ${this.currentTheme === theme.id ? 'active' : ''}`;
            option.innerHTML = `
                <span class="theme-name">${theme.name}</span>
                <small class="theme-description">${theme.description}</small>
            `;
            option.addEventListener('click', () => this.applyTheme(theme.id));
            optionsContainer.appendChild(option);
        });

        // Add color pickers for customizable themes
        const colorPickers = container.querySelector('.color-pickers');
        const currentTheme = this.themeConfig.available_themes.find(t => t.id === this.currentTheme);
        if (currentTheme && currentTheme.customizable) {
            Object.entries(currentTheme.colors).forEach(([name, color]) => {
                const picker = document.createElement('div');
                picker.className = 'color-picker';
                picker.innerHTML = `
                    <label>${name.charAt(0).toUpperCase() + name.slice(1)}</label>
                    <input type="color" name="${name}" value="${this.customColors[name] || color}">
                `;
                picker.querySelector('input').addEventListener('change', (e) => {
                    this.customColors[name] = e.target.value;
                    this.applyCustomColors();
                });
                colorPickers.appendChild(picker);
            });
        }

        // Add event listeners
        container.querySelector('.font-selector').addEventListener('change', (e) => {
            this.applyFont(e.target.value);
        });

        container.querySelector('.size-selector').addEventListener('change', (e) => {
            this.applySize(e.target.value);
        });

        // Add tab switching
        container.querySelectorAll('.theme-tabs button').forEach(button => {
            button.addEventListener('click', () => {
                container.querySelectorAll('.theme-tabs button').forEach(b => b.classList.remove('active'));
                container.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active'));
                button.classList.add('active');
                container.querySelector(`#${button.dataset.tab}`).classList.add('active');
            });
        });

        // Add toggle functionality
        const toggle = container.querySelector('.theme-switcher-toggle');
        toggle.addEventListener('click', () => {
            container.classList.toggle('open');
        });

        // Add to document
        document.body.appendChild(container);

        // Add styles
        this.addThemeSwitcherStyles();
    }

    addThemeSwitcherStyles() {
        const styles = `
            .theme-switcher {
                position: fixed;
                right: 20px;
                bottom: 20px;
                z-index: 1000;
            }

            .theme-switcher-toggle {
                width: 40px;
                height: 40px;
                background: var(--primary-color);
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                box-shadow: 0 2px 5px rgba(0,0,0,0.2);
                color: white;
            }

            .theme-switcher-menu {
                position: absolute;
                bottom: 50px;
                right: 0;
                background: var(--body-bg);
                border-radius: 8px;
                padding: 15px;
                min-width: 250px;
                box-shadow: 0 2px 10px rgba(0,0,0,0.1);
                display: none;
                border: 1px solid var(--border-color);
            }

            .theme-switcher.open .theme-switcher-menu {
                display: block;
            }

            .theme-tabs {
                display: flex;
                margin-bottom: 15px;
                border-bottom: 1px solid var(--border-color);
            }

            .theme-tabs button {
                background: none;
                border: none;
                padding: 8px 15px;
                cursor: pointer;
                color: var(--text-color);
                opacity: 0.7;
            }

            .theme-tabs button.active {
                opacity: 1;
                border-bottom: 2px solid var(--primary-color);
            }

            .tab-pane {
                display: none;
            }

            .tab-pane.active {
                display: block;
            }

            .theme-switcher h6 {
                margin: 0 0 10px;
                color: var(--text-color);
                font-size: 14px;
            }

            .theme-option {
                padding: 10px;
                cursor: pointer;
                border-radius: 4px;
                margin-bottom: 5px;
            }

            .theme-option:hover {
                background: rgba(var(--primary-color-rgb), 0.1);
            }

            .theme-option.active {
                background: var(--primary-color);
                color: white;
            }

            .theme-name {
                display: block;
                font-weight: 500;
                margin-bottom: 2px;
            }

            .theme-description {
                display: block;
                font-size: 12px;
                opacity: 0.8;
            }

            .customize-section {
                margin-bottom: 15px;
            }

            .customize-section label {
                display: block;
                margin-bottom: 5px;
                color: var(--text-color);
                font-size: 13px;
            }

            .customize-section select {
                width: 100%;
                padding: 6px;
                border-radius: 4px;
                border: 1px solid var(--border-color);
                background: var(--body-bg);
                color: var(--text-color);
            }

            .color-pickers {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 10px;
            }

            .color-picker {
                display: flex;
                flex-direction: column;
            }

            .color-picker input {
                width: 100%;
                height: 30px;
                padding: 0;
                border: none;
                border-radius: 4px;
                cursor: pointer;
            }
        `;

        const styleSheet = document.createElement('style');
        styleSheet.textContent = styles;
        document.head.appendChild(styleSheet);
    }

    applyTheme(themeId) {
        const theme = this.themeConfig.available_themes.find(t => t.id === themeId);
        if (!theme) return;

        // Remove current theme
        const currentThemeLink = document.getElementById('theme-css');
        if (currentThemeLink) {
            currentThemeLink.remove();
        }

        // Add new theme
        const link = document.createElement('link');
        link.id = 'theme-css';
        link.rel = 'stylesheet';
        link.href = `../ui/styles/themes/${theme.css}`;
        document.head.appendChild(link);

        // Save preference
        localStorage.setItem('isp-theme', themeId);
        this.currentTheme = themeId;

        // Apply custom colors if theme is customizable
        if (theme.customizable) {
            this.applyCustomColors();
        }

        // Update active state in UI
        document.querySelectorAll('.theme-option').forEach(option => {
            option.classList.toggle('active', option.querySelector('.theme-name').textContent === theme.name);
        });
    }

    applyFont(fontFamily) {
        document.documentElement.style.setProperty('--font-family', fontFamily);
        localStorage.setItem('isp-font', fontFamily);
        this.currentFont = fontFamily;
    }

    applySize(size) {
        const sizeValue = this.themeConfig.size_options[size];
        document.documentElement.style.setProperty('--base-size', sizeValue);
        localStorage.setItem('isp-size', size);
        this.currentSize = size;
    }

    applyCustomColors() {
        const theme = this.themeConfig.available_themes.find(t => t.id === this.currentTheme);
        if (!theme || !theme.customizable) return;

        Object.entries(theme.colors).forEach(([name, defaultColor]) => {
            const color = this.customColors[name] || defaultColor;
            document.documentElement.style.setProperty(`--${name}-color`, color);
        });

        localStorage.setItem('isp-custom-colors', JSON.stringify(this.customColors));
    }
}

// Initialize theme switcher when document is ready
document.addEventListener('DOMContentLoaded', () => {
    window.themeSwitcher = new ThemeSwitcher();
});
