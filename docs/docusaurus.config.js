// @ts-check

/** @type {import('@docusaurus/types').Config} */
const config = {
  title: 'RAG-PHP',
  tagline: 'The reference Retrieval-Augmented Generation component for PHP',
  favicon: 'img/favicon.ico',

  url: 'https://rag-php.github.io',
  baseUrl: '/rag-php/',

  organizationName: 'rag-php',
  projectName: 'rag-php',

  onBrokenLinks: 'throw',
  onBrokenMarkdownLinks: 'warn',

  i18n: {
    defaultLocale: 'en',
    locales: ['en'],
  },

  presets: [
    [
      'classic',
      /** @type {import('@docusaurus/preset-classic').Options} */
      ({
        docs: {
          sidebarPath: './sidebars.js',
          editUrl: 'https://github.com/rag-php/rag-php/tree/main/docs/',
        },
        blog: false,
        theme: {
          customCss: './src/css/custom.css',
        },
      }),
    ],
  ],

  themeConfig:
    /** @type {import('@docusaurus/preset-classic').ThemeConfig} */
    ({
      navbar: {
        title: 'RAG-PHP',
        logo: {
          alt: 'RAG-PHP Logo',
          src: 'img/logo.svg',
        },
        items: [
          {
            type: 'docSidebar',
            sidebarId: 'docs',
            position: 'left',
            label: 'Documentation',
          },
          {
            href: 'https://github.com/rag-php/rag-php',
            label: 'GitHub',
            position: 'right',
          },
        ],
      },
      footer: {
        style: 'dark',
        links: [
          {
            title: 'Docs',
            items: [
              { label: 'Getting Started', to: '/docs/getting-started' },
              { label: 'API Reference', to: '/docs/api/embedder' },
            ],
          },
          {
            title: 'Community',
            items: [
              { label: 'GitHub', href: 'https://github.com/rag-php/rag-php' },
              { label: 'Discord', href: 'https://discord.gg/rag-php' },
            ],
          },
        ],
        copyright: `Copyright © ${new Date().getFullYear()} RAG-PHP Contributors. Built with Docusaurus.`,
      },
      prism: {
        theme: require('prism-react-renderer').themes.github,
        darkTheme: require('prism-react-renderer').themes.dracula,
        additionalLanguages: ['php', 'bash', 'yaml'],
      },
    }),
};

module.exports = config;
