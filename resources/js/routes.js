export default [
    {path: '/', redirect: '/posts'},

    // Posts...
    {
        path: '/posts',
        name: 'posts',
        component: require('./screens/posts/index').default
    },
    {
        path: '/posts/new',
        name: 'post-new',
        component: require('./screens/posts/edit').default
    },
    {
        path: '/posts/:id',
        name: 'post-edit',
        component: require('./screens/posts/edit').default
    },


    // Categories...
    {
        path: '/tags',
        name: 'tags',
        component: require('./screens/tags/index').default
    },

    {
        path: '/tags/new',
        name: 'tag-new',
        component: require('./screens/tags/edit').default
    },

    {
        path: '/tags/:id',
        name: 'tag-edit',
        component: require('./screens/tags/edit').default
    },


    // Authors...
    {
        path: '/team',
        name: 'team',
        component: require('./screens/team/index').default
    },

    {
        path: '/team/new',
        name: 'team-new',
        component: require('./screens/team/edit').default
    },

    {
        path: '/team/:id',
        name: 'team-edit',
        component: require('./screens/team/edit').default
    },


    // Pages...
    {
        path: '/pages',
        name: 'pages',
        component: require('./screens/pages/index').default
    },
    {
        path: '/pages/new',
        name: 'page-new',
        component: require('./screens/pages/edit').default
    },
    {
        path: '/pages/:id',
        name: 'page-edit',
        component: require('./screens/pages/edit').default
    },

    // Categories...
    {
        path: '/categories',
        name: 'categories',
        component: require('./screens/categories/index').default
    },

    {
        path: '/categories/new',
        name: 'category-new',
        component: require('./screens/categories/edit').default
    },

    {
        path: '/categories/:id',
        name: 'category-edit',
        component: require('./screens/categories/edit').default
    },


    // Catch All...
    {
        path: '*',
        name: 'catch-all',
        component: require('./screens/404').default,
    },
];
