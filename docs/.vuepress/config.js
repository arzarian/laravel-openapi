module.exports = {
  base: '/laravel-openapi/',
  title: 'Laravel OpenAPI',
  description: 'Generate OpenAPI specification for Laravel Applications',
  themeConfig: {
    nav: [
      {text: 'Home', link: '/'},
      {text: 'GitHub', link: 'https://github.com/arzarian/laravel-openapi'},
      {
        text: 'Packagist',
        link: 'https://packagist.org/packages/arzarian/laravel-openapi',
      },
    ],
    sidebar: [
      '/',
      {
        title: 'Paths',
        collapsable: false,
        children: [
          '/paths/operations',
          '/paths/parameters',
          '/paths/request-bodies',
          '/paths/responses',
          '/paths/callbacks',
        ],
      },
      '/configuration',
      '/schemas',
      '/components',
      '/collections',
      '/middlewares',
      '/security',
      '/extensions',
      '/migration'
    ],
    displayAllHeaders: true,
    sidebarDepth: 2,
  },
};
