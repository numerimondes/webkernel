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

    [lang="ar"] h1,
    [lang="ar"] h2,
    [lang="ar"] h3 {
    font-family: 'Amiri', serif !important;
    }

    [lang="he"] h1,
    [lang="he"] h2,
    [lang="he"] h3 {
        font-family: 'Frank+Ruhl+Libre', serif;
    }
    [lang="ja"] {
        font-family: 'Noto Sans JP', sans-serif;
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
