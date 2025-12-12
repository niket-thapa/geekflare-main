const plugin = require("tailwindcss/plugin");
const defaultTheme = require("tailwindcss/defaultTheme");

/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./**/*.php",
    "./template-parts/**/*.php",
    "./blocks/**/*.{php,js}",
    "./src/**/*.{js,jsx,ts,tsx}",
    // Explicitly include insights-section block files
    "./blocks/insights-section/**/*.{php,js}",
    // Explicitly include articles-section block files
    "./blocks/articles-section/**/*.{php,js}",
    // Explicitly include articles-carousel block files
    "./blocks/articles-carousel/**/*.{php,js}",
  ],
  safelist: [
    // Common utility classes that might be dynamically generated
    "rounded-full",
    "object-cover",
    "text-primary-hover",
    "hover:text-primary-hover",
    // WordPress Button Block classes
    "wp-block-button",
    "wp-block-button__link",
    "is-style-fill",
    "is-style-outline",
    // Yoast FAQ Block classes
    "schema-faq",
    "wp-block-yoast-faq-block",
    "schema-faq-section",
    "schema-faq-question",
    "faq-item",
    "faq-header",
    "faq-content",
    "faq-chevron",
    "is-open",
    // Flickity carousel classes (added dynamically by JavaScript)
    "flickity-enabled",
    "flickity-page-dots",
    "dot",
    "meet-experts-carousel",
    "feature-articles-carousel",
    "articles-carousel",
    "articles-carousel",
    // Insights Section Block - Base classes
    "insights-section",
    "sticky_posts",
    "other_posts",
    // Articles Section Block - Base classes
    "articles-section",
    // Article Carousel Block - Base classes
    "articles-carousel",
    "security-guides-section",
    // Layout classes
    "flex",
    "flex-col",
    "min-w-0",
    "relative",
    "absolute",
    // container-1056 classes
    "container-1056",
    // Spacing classes - Base
    "gap-2",
    "gap-4",
    "gap-6",
    "gap-8",
    "gap-12",
    "gap-16",
    "gap-20",
    "gap-24",
    "p-6",
    "py-2",
    "py-3",
    "py-16",
    "pb-8",
    "px-2.5",
    "-m-6",
    "m-0.25",
    "mt-1",
    // Spacing classes - Responsive (md)
    "md:py-24",
    "md:py-3",
    "md:py-2",
    "md:mt-1",
    // Spacing classes - Responsive (lg)
    "lg:gap-16",
    "lg:gap-12",
    // Spacing classes - Responsive (xl)
    "xl:gap-24",
    "xl:gap-16",
    "xl:gap-12",
    // Spacing classes - Responsive (md) - Additional
    "md:gap-12",
    "md:gap-14",
    "md:gap-3",
    "md:gap-4",
    "md:gap-5.5",
    "md:gap-6",
    // Gap variants
    "gap-1",
    "gap-5.5",
    "gap-14",
    // Grid classes - Base
    "grid",
    "grid-cols-2",
    "col-span-full",
    // Grid classes - Responsive (lg)
    "lg:grid",
    "lg:grid-cols-2",
    "lg:grid-cols-3",
    // Grid classes - Responsive (md)
    "md:grid",
    "md:grid-cols-2",
    "md:gap-14",
    // Order classes - Responsive (lg)
    "lg:order-2",
    // Display classes
    "inline-flex",
    "items-center",
    "items-end",
    "justify-center",
    "text-center",
    "text-left",
    "text-right",
    // Justify classes - Responsive (md)
    "md:justify-between",
    // Sizing classes
    "w-full",
    "h-4",
    "w-4",
    "max-w-[66rem]",
    "w-[23rem]",
    "px-6",
    // Typography classes - Base
    "text-start",
    "text-xs",
    "text-sm",
    "text-lg",
    "text-xl",
    "text-2xl",
    "text-3xl",
    "text-5xl",
    "font-bold",
    "font-semibold",
    "font-medium",
    "font-gilroy",
    "leading-none",
    "leading-5",
    "leading-[1.25]",
    "tracking-1p",
    "tracking-2p",
    "tracking-widest",
    "uppercase",
    "line-clamp-1",
    // Typography classes - Responsive (md)
    "md:text-2xl",
    "md:text-5xl",
    "md:text-xl",
    "md:text-base",
    "md:leading-none",
    "md:-tracking-2p",
    "md:tracking-1p",
    // Color classes
    "bg-gray-25",
    "bg-white",
    "text-gray-900",
    "text-gray-500",
    "text-primary",
    // Border classes
    "rounded-2xl",
    "border-b",
    "border-gray-200",
    // Shadow classes
    "shadow-sm",
    // Position classes
    "top-3",
    "start-3",
    // Overflow classes
    "overflow-hidden",
    // Button classes (component classes - ensure they're always included)
    "btn",
    "btn--primary",
    "btn--secondary",
    "btn--outline",
    "btn-icon",
    "btn-logo-icon",
    // Product Compare Table classes
    "product-compare-table",
    // Button utility classes used in @apply
    "text-sm",
    "lg:text-base",
    "font-gilroy",
    "font-semibold",
    "px-5",
    "lg:px-6",
    "py-3",
    "lg:py-3.5",
    "flex",
    "items-center",
    "gap-1.5",
    "md:gap-2",
    "transition-colors",
    "duration-300",
    "ease-linear",
    "outline-none",
    "shadow-none",
    "bg-primary",
    "hover:bg-primary-hover",
    "text-white",
    "bg-gray-100",
    "hover:bg-gray-200",
    "text-gray-700",
    "border",
    "border-gray-200",
    "hover:border-primary",
    "hover:text-primary",
    "w-3.5",
    "h-3.5",
    "md:h-4",
    "md:w-4",
    "md:h-4.5",
    "md:w-4.5",
    // Responsive spacing - max-sm (explicitly safelisted)
    "max-sm:py-2",
    "max-sm:px-3",
    // Score breakdown color classes - explicitly listed for reliability
    "bg-success-600",
    "bg-success-50",
    "bg-success-100",
    "bg-success-300",
    "bg-warning-600",
    "bg-warning-50",
    "bg-warning-100",
    "bg-warning-300",
    "bg-error-600",
    "bg-error-50",
    "bg-pricing-100",
    "bg-pricing-300",
    "text-success-300",
    "text-warning-300",
    "text-pricing-300",
    "rounded-l-full",
    // Safelist patterns for all responsive variants
    {
      pattern: /^(flex|flex-col|grid|inline-flex|block|inline-block|hidden)$/,
      variants: ["sm", "md", "lg", "xl"],
    },
    {
      pattern: /^(gap|space-[xy])-(0|1|2|3|4|5\.5|6|8|12|14|16|20|24)$/,
      variants: ["sm", "md", "lg", "xl", "max-sm"],
    },
    {
      pattern:
        /^(p|px|py|pt|pb|pl|pr|m|mx|my|mt|mb|ml|mr)-(0|0\.25|2|3|4|6|8|12|16|24)$/,
      variants: ["sm", "md", "lg", "xl", "max-sm"],
    },
    {
      pattern: /^grid-cols-\d+$/,
      variants: ["sm", "md", "lg", "xl"],
    },
    {
      pattern: /^order-\d+$/,
      variants: ["sm", "md", "lg", "xl"],
    },
    {
      pattern: /^text-(xs|sm|base|lg|xl|2xl|3xl|4xl|5xl|6xl)$/,
      variants: ["sm", "md", "lg", "xl"],
    },
    {
      pattern: /^-(tracking|leading)-\w+$/,
      variants: ["sm", "md", "lg", "xl"],
    },
    // Score breakdown dynamic color classes
    {
      pattern: /^bg-(success|warning|error)-(50|600)$/,
    },
  ],
  theme: {
    fontFamily: {
      gilroy: ["Gilroy", "sans-serif"],
      sans: ["Gilroy", "sans-serif"],
    },
    fontWeight: {
      thin: 100,
      extralight: 200,
      light: 300,
      normal: 400,
      medium: 500,
      semibold: 600,
      bold: 700,
      extrabold: 800,
      heavy: 900,
      black: 950,
    },
    extend: {
      colors: {
        primary: "#FF4A00",
        "primary-hover": "#E84300",
        "eva-prime": {
          50: "#FFEDE6",
          100: "#FFE8E0",
          200: "#FFAC8A",
          300: "#FFB399",
          400: "#FF8A66",
          500: "#FF6B3D",
          600: "#E84300",
          700: "#E63900",
          800: "#CC3300",
          900: "#B32E00",
        },
        gray: {
          25: "#FCFCFF",
          50: "#FAFAFA",
          100: "#F5F5F5",
          200: "#E9EAEB",
          300: "#D0D5DD",
          400: "#98A2B3",
          500: "#717680",
          600: "#535862",
          700: "#414651",
          800: "#252B37",
          900: "#181D27",
        },
        success: {
          50: "#D1FADF",
          100: "#ECFDF5",
          200: "#A6F4C5",
          300: "#047857",
          600: "#039855",
        },
        warning: {
          50: "#FEF0C7",
          100: "#FEF2F2",
          300: "#DC6803",
          600: "#DC6803",
        },
        pricing: {
          100: "#EFF6FF",
          300: "#1D4ED8",
        },
        error: {
          50: "#FEE4E2",
          600: "#F04438",
        },
        rating: {
          50: "#FFFAEB",
          border: "#FEDF89",
          star: "#F79009",
        },
      },
      lineHeight: {
        none: "1.1",
        4.5: "1.125rem",
        5.5: "1.375rem",
      },
      letterSpacing: {
        "1p": "0.01em",
        "2p": "0.02em",
      },
      fontSize: {
        "7.5xl": ["4.75rem", { lineHeight: "1" }],
      },
      spacing: {
        0.25: "0.0625rem",
        4.5: "1.125rem",
        5.5: "1.375rem",
        6.5: "1.625rem",
        7.5: "1.875rem",
      },
      minHeight: {},
      minWidth: {},
      boxShadow: {},
      borderRadius: {
        "4xl": "2rem",
      },
      gridTemplateAreas: {},
      zIndex: {
        1: "1",
        1000: "1000",
        1100: "1100",
        1200: "1200",
        1300: "1300",
        1400: "1400",
        1500: "1500",
      },
      transitionProperty: {
        dropdown: "opacity, visibility, transform",
        background: "background-color",
      },
      backgroundImage: {},
      transitionTimingFunction: {
        standard: "cubic-bezier(0.30, 0.00, 0.70, 1.00)",
        "standard-decelerate": "cubic-bezier(0.30, 0.00, 0.10, 1.00)",
        "emphasized-decelerate": "cubic-bezier(0.50, 0.00, 0.10, 1.00)",
        "standard-accelerate": "cubic-bezier(0.90, 0.00, 0.70, 1.00)",
      },
      transitionDuration: {
        instant: "0ms",
        "quick-1": "100ms",
        "quick-2": "300ms",
        "standard-1": "400ms",
        "standard-2": "500ms",
        "extended-1": "700ms",
      },
      transitionDelay: {
        none: "0ms",
        "quick-1": "25ms",
        "quick-2": "50ms",
        "standard-1": "100ms",
        "standard-2": "200ms",
        "extended-1": "400ms",
        "extended-2": "600ms",
        "extended-3": "1000ms",
      },
      screens: {
        "max-lg": { max: "1023px" },
        "max-sm": { max: "639px" },
      },
      typography: (theme) => ({
        DEFAULT: {
          css: {
            // Or use your full custom selector
            ":where(a):not(:where([class~=not-prose], [class~=btn],[class~=not-prose] *))":
              {
                color: theme("primary"),
                textDecoration: "none",
              },
            ":where(li):not(:where([class~=not-prose],[class~=not-prose] *))": {
              marginTop: "0",
              marginBottom: "0",
              paddingLeft: "0",
            },
          },
        },
      }),
    },
  },
  plugins: [
    // Fixed-width containers (inner width + 24px padding each side)
    plugin(function ({ addComponents }) {
      const containers = {
        ".container-1056": {
          maxWidth: "1104px", // 1056 + 24*2
          marginLeft: "auto",
          marginRight: "auto",
          paddingLeft: "24px",
          paddingRight: "24px",
        },
        ".container": {
          maxWidth: "1112px", // 1056 + 24*2
          marginLeft: "auto",
          marginRight: "auto",
          paddingLeft: "24px",
          paddingRight: "24px",
        },
        ".container-1184": {
          maxWidth: "1232px", // 1184 + 48
          marginLeft: "auto",
          marginRight: "auto",
          paddingLeft: "24px",
          paddingRight: "24px",
        },
        ".container-1216": {
          maxWidth: "1264px", // 1216 + 48
          marginLeft: "auto",
          marginRight: "auto",
          paddingLeft: "24px",
          paddingRight: "24px",
        },
        ".container-1280": {
          maxWidth: "1328px", // 1280 + 48
          marginLeft: "auto",
          marginRight: "auto",
          paddingLeft: "24px",
          paddingRight: "24px",
        },
      };
      addComponents(containers);
    }),
    require("tailwind-scrollbar-hide"),
    require("@tailwindcss/line-clamp"),
    require("@savvywombat/tailwindcss-grid-areas"),
    require("@tailwindcss/typography"),
    plugin(function ({ addBase, theme }) {
      const rootVars = {};
      const bg = theme("backgroundImage");
      Object.entries(bg).forEach(([key, value]) => {
        rootVars[`--bg-${key}`] = value;
      });
      const colors = theme("colors");
      function flattenColors(prefix, obj) {
        Object.entries(obj).forEach(([name, val]) => {
          if (typeof val === "string") {
            const varName =
              name === "DEFAULT" ? prefix : prefix ? `${prefix}-${name}` : name;
            rootVars[`--bg-${varName}`] = val;
          } else {
            const nextPrefix =
              name === "DEFAULT" ? prefix : prefix ? `${prefix}-${name}` : name;
            flattenColors(nextPrefix, val);
          }
        });
      }
      flattenColors("", colors);
      addBase({
        ":root": rootVars,
      });
    }),
  ],
  variants: {
    extend: {
      transitionTimingFunction: ["responsive", "hover", "focus"],
      transitionDuration: ["responsive", "hover", "focus"],
      transitionDelay: ["responsive", "hover", "focus"],
    },
  },
};
