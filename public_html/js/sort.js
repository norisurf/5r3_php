/**
 * 在庫車両ソート機能
 */
function sortVehicles(sortBy) {
    var grid = document.getElementById('vehicle-grid');
    if (!grid) return;

    var cards = Array.from(grid.querySelectorAll('.vehicle-card'));
    if (cards.length === 0) return;

    cards.sort(function(a, b) {
        switch(sortBy) {
            case 'price_high':
                return parseInt(b.dataset.price || 0) - parseInt(a.dataset.price || 0);
            case 'price_low':
                return parseInt(a.dataset.price || 0) - parseInt(b.dataset.price || 0);
            case 'year_new':
                return parseInt(b.dataset.year || 0) - parseInt(a.dataset.year || 0);
            case 'mileage_low':
                return parseInt(a.dataset.mileage || 0) - parseInt(b.dataset.mileage || 0);
            case 'newest':
                return new Date(b.dataset.created) - new Date(a.dataset.created);
            default:
                return 0;
        }
    });

    cards.forEach(function(card) {
        grid.appendChild(card);
    });

    // ボタンのアクティブ状態を更新
    document.querySelectorAll('.sort-btn').forEach(function(btn) {
        if (btn.dataset.sort === sortBy) {
            btn.className = 'sort-btn active px-4 py-2 rounded-full text-sm font-bold transition-all bg-slate-900 text-white shadow-lg';
        } else {
            btn.className = 'sort-btn px-4 py-2 rounded-full text-sm font-bold transition-all bg-slate-100 text-slate-600 hover:bg-slate-200';
        }
    });
}
