<div x-data="{ max: 4, showAll: false }" class="system-news-row-wrapper">
    <div class="system-news-row" :class="{ 'overflow-x-auto': !showAll }">
        <template x-for="(news, i) in showAll ? $store.systemNews : $store.systemNews.slice(0, max)" :key="i">
            <div class="system-news-card">
                <div class="system-news-image-wrapper">
                    <img :src="news.image" alt="Image actu" class="system-news-image">
                </div>
                <div class="system-news-content">
                    <div class="system-news-title" x-text="news.title"></div>
                    <div class="system-news-text" x-text="news.text"></div>
                </div>
            </div>
        </template>
        <button x-show="!showAll && $store.systemNews.length > max" @click="showAll = true" class="system-news-view-more">
            Voir plus
        </button>
    </div>
</div>
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.store('systemNews', [
            { image: 'https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=200&q=80', title: 'Export de données disponible', text: 'Vous pouvez désormais exporter vos données personnelles.' },
            { image: 'https://images.unsplash.com/photo-1465101046530-73398c7f28ca?auto=format&fit=crop&w=200&q=80', title: 'Maintenance prévue', text: 'Le 15/06 à 22h, le service sera indisponible pendant 30 min.' },
            { image: 'https://images.unsplash.com/photo-1519125323398-675f0ddb6308?auto=format&fit=crop&w=200&q=80', title: 'Bienvenue aux nouveaux membres', text: 'La communauté s’agrandit, merci à tous !' },
            { image: 'https://images.unsplash.com/photo-1504384308090-c894fdcc538d?auto=format&fit=crop&w=200&q=80', title: 'Mise à jour de sécurité', text: 'Une mise à jour de sécurité a été appliquée avec succès.' },
            { image: 'https://images.unsplash.com/photo-1465101178521-c1a9136a3b99?auto=format&fit=crop&w=200&q=80', title: 'Nouvelle interface mobile', text: 'Découvrez notre nouvelle expérience sur mobile.' },
            { image: 'https://images.unsplash.com/photo-1465101046530-73398c7f28ca?auto=format&fit=crop&w=200&q=80', title: 'Sondage utilisateur', text: 'Participez à notre sondage et aidez-nous à nous améliorer.' },
        ]);
    });
</script>
<style>
.system-news-row-wrapper {
    overflow-x: auto;
    padding-bottom: 0.5rem;
}
.system-news-row {
    display: flex;
    gap: 1rem;
    align-items: stretch;
    min-height: 140px;
}
.system-news-card {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.07);
    min-width: 220px;
    max-width: 240px;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    transition: box-shadow 0.2s, transform 0.2s;
}
.system-news-card:hover {
    box-shadow: 0 6px 18px rgba(0,0,0,0.13);
    transform: translateY(-2px) scale(1.03);
}
.system-news-image-wrapper {
    width: 100%;
    aspect-ratio: 3/2;
    overflow: hidden;
    background: #f3f4f6;
}
.system-news-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}
.system-news-content {
    padding: 0.75rem 1rem 1rem 1rem;
    display: flex;
    flex-direction: column;
    gap: 0.3rem;
}
.system-news-title {
    font-weight: 600;
    font-size: 1.05em;
    color: #1e293b;
    margin-bottom: 0.1em;
}
.system-news-text {
    font-size: 0.95em;
    color: #64748b;
}
.system-news-view-more {
    align-self: center;
    background: none;
    border: none;
    color: #2563eb;
    cursor: pointer;
    font-size: 1em;
    font-weight: 500;
    margin-left: 0.5rem;
    padding: 0.5rem 1.2rem;
    border-radius: 9999px;
    transition: background 0.15s;
}
.system-news-view-more:hover {
    background: #e0e7ff;
}
@media (max-width: 600px) {
    .system-news-card { min-width: 70vw; max-width: 80vw; }
}
</style>
