import 'vuetify/styles'; // Importa los estilos de Vuetify
import { createVuetify } from 'vuetify';
import { mdi } from 'vuetify/iconsets/mdi';
import * as components from 'vuetify/components';
import * as directives from 'vuetify/directives';

const vuetify = createVuetify({
    components,
    directives,
    icons: { 
        defaultSet: 'mdi',
        sets: {
            mdi,
        },
    },
    theme: {
        themes: {
            light: {
                dark: false,
                colors: {
                    primary: '#E53935',
                },
            },
        },
    },
});

export default vuetify;
