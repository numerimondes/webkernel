@push('styles')
<style>
    /*--- Arabic ---*/
    @import url('https://fonts.googleapis.com/css2?family=Amiri&display=swap');
    /*--- Hebrew -----*/
    @import url('https://fonts.googleapis.com/css2?family=Assistant&display=swap');
    @import url('https://fonts.googleapis.com/css2?family=Frank+Ruhl+Libre:wght@300..900&display=swap');
    @import url('https://fonts.googleapis.com/css2?family=Noto+Serif+Hebrew:wght@100..900&display=swap');
    /*--- Japanese ---*/
    @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+JP&display=swap');

    [lang="ar"] {
      /*  font-family: 'Amiri', serif;*/
    }

    [lang="ar"] h1,
    [lang="ar"] h2,
    [lang="ar"] h3 {
    font-family: 'Amiri', serif !important;
    }

    [lang="he"] {
        font-family: 'Frank+Ruhl+Libre', serif;
    }
    [lang="ja"] {
        font-family: 'Noto Sans JP', sans-serif;
    }

    aside,
    [lang="ar"] aside,
    [lang="he"] aside,
    [lang="ja"] aside {
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif !important;
    }

    .force-inter,
    .force-inter :not(aside):not(aside *) {
        font-family: 'Inter', sans-serif !important;
    }

    .force-inter-ltr,
    .force-inter-ltr :not(aside):not(aside *) {
        font-family: 'Inter', sans-serif !important;
        direction: ltr !important;
    }
</style>
@endpush
