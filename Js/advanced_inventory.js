/**
 * Sistema Avanzado de Inventario con Rotación - PARTE 1
 * Funcionalidades principales y configuración inicial
 */

class InventorySystem {
    constructor() {
        this.selectedStyles = [];
        this.allStyles = [];
        this.tableData = [];
        this.filteredData = [];
        this.sortColumn = null;
        this.sortDirection = 'asc';
        this.currentPage = 1;
        this.itemsPerPage = 50;
        
        this.init();
    }

    init() {
        this.extractTableData();
        this.setupEventListeners();
        this.setupMultipleSearch();
        this.setupTableSorting();
        this.setupPagination();
        this.setupAdvancedFilters();
        this.setupTooltips();
        this.setupKeyboardShortcuts();
        this.calculateAdvancedMetrics();
    }

    // Extraer datos de la tabla para manipulación
    extractTableData() {
        const table = document.getElementById('dataTable');
        if (!table) return;

        const rows = table.getElementsByTagName('tr');
        
        for (let i = 2; i < rows.length; i++) {
            const cells = rows[i].getElementsByTagName('td');
            if (cells.length > 2) {
                const estilo = cells[2].textContent.trim();
                if (estilo && !this.allStyles.includes(estilo)) {
                    this.allStyles.push(estilo);
                }

                const rowData = {
                    genero: cells[0]?.textContent.trim() || '',
                    categoria: cells[1]?.textContent.trim() || '',
                    estilo: cells[2]?.textContent.trim() || '',
                    color: cells[3]?.textContent.trim() || '',
                    precio: this.parsePrice(cells[4]?.textContent.trim() || '0'),
                    ventas: this.extractStoreData(cells, 'venta'),
                    stocks: this.extractStoreData(cells, 'stock'),
                    rotaciones: this.extractStoreData(cells, 'rotacion'),
                    element: rows[i]
                };
                
                this.tableData.push(rowData);
            }
        }
        
        this.allStyles.sort();
        this.filteredData = [...this.tableData];
    }

    extractStoreData(cells, type) {
        const data = {};
        let cellIndex = 5;
        
        const tiendas = this.getTiendaNumbers();
        tiendas.forEach(tienda => {
            switch(type) {
                case 'venta':
                    data[tienda] = this.parseNumber(cells[cellIndex]?.textContent || '0');
                    break;
                case 'stock':
                    data[tienda] = this.parseNumber(cells[cellIndex + 1]?.textContent || '0');
                    break;
                case 'rotacion':
                    data[tienda] = this.parseFloat(cells[cellIndex + 2]?.textContent || '0');
                    break;
            }
            cellIndex += 3;
        });
        
        return data;
    }

    getTiendaNumbers() {
        const tiendas = [];
        const headers = document.querySelectorAll('th.store-column');
        headers.forEach(header => {
            const match = header.textContent.match(/TIENDA(\d+)/);
            if (match) {
                tiendas.push(match[1]);
            }
        });
        return tiendas;
    }

    setupMultipleSearch() {
        const searchInput = document.getElementById('searchInput');
        const suggestions = document.getElementById('suggestions');
        
        if (!searchInput || !suggestions) return;

        let searchTimeout;

        searchInput.addEventListener('input', (e) => {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                this.handleSearchInput(e.target.value);
            }, 300);
        });

        searchInput.addEventListener('keydown', (e) => {
            this.handleSearchKeydown(e);
        });

        document.addEventListener('click', (e) => {
            if (!e.target.closest('.search-multiple')) {
                suggestions.style.display = 'none';
            }
        });
    }

    handleSearchInput(value) {
        const suggestions = document.getElementById('suggestions');
        value = value.toLowerCase().trim();
        
        if (value.length === 0) {
            suggestions.style.display = 'none';
            this.applyGeneralSearch('');
            return;
        }

        if (this.selectedStyles.length === 0) {
            this.applyGeneralSearch(value);
        }

        const matches = this.allStyles.filter(style => 
            style.toLowerCase().includes(value) && 
            !this.selectedStyles.includes(style)
        ).slice(0, 10);

        if (matches.length > 0) {
            suggestions.innerHTML = matches.map(style => 
                `<div class="suggestion-item" onclick="inventorySystem.addStyle('${style}')">${this.highlightMatch(style, value)}</div>`
            ).join('');
            suggestions.style.display = 'block';
        } else {
            suggestions.style.display = 'none';
        }
    }

    highlightMatch(text, search) {
        const regex = new RegExp(`(${search})`, 'gi');
        return text.replace(regex, '<strong>$1</strong>');
    }

    handleSearchKeydown(e) {
        if (e.key === 'Enter' || e.key === ',') {
            e.preventDefault();
            const value = e.target.value.trim();
            if (value && !this.selectedStyles.includes(value)) {
                this.addStyle(value);
            }
        } else if (e.key === 'Escape') {
            document.getElementById('suggestions').style.display = 'none';
        } else if (e.key === 'ArrowDown' || e.key === 'ArrowUp') {
            const suggestions = document.getElementById('suggestions');
            if (suggestions.style.display === 'block') {
                e.preventDefault();
                this.navigateSuggestions(e.key === 'ArrowDown' ? 1 : -1);
            }
        }
    }

    navigateSuggestions(direction) {
        const suggestions = document.querySelectorAll('.suggestion-item');
        if (suggestions.length === 0) return;

        let currentIndex = -1;
        suggestions.forEach((item, index) => {
            if (item.classList.contains('selected')) {
                currentIndex = index;
                item.classList.remove('selected');
            }
        });

        const newIndex = Math.max(0, Math.min(suggestions.length - 1, currentIndex + direction));
        suggestions[newIndex].classList.add('selected');
        suggestions[newIndex].scrollIntoView({ block: 'nearest' });
    }

    addStyle(style) {
        if (!this.selectedStyles.includes(style)) {
            this.selectedStyles.push(style);
            this.updateSelectedStyles();
            this.applyStyleFilter();
        }
        
        document.getElementById('searchInput').value = '';
        document.getElementById('suggestions').style.display = 'none';
    }

    updateSelectedStyles() {
        const container = document.getElementById('selectedStyles');
        if (!container) return;

        container.innerHTML = this.selectedStyles.map(style => 
            `<div class="style-tag">
                ${style}
                <span class="remove" onclick="inventorySystem.removeStyle('${style}')">×</span>
            </div>`
        ).join('');
    }

    removeStyle(style) {
        this.selectedStyles = this.selectedStyles.filter(s => s !== style);
        this.updateSelectedStyles();
        this.applyStyleFilter();
    }

    applyStyleFilter() {
        if (this.selectedStyles.length === 0) {
            this.showAllRows();
            return;
        }

        this.tableData.forEach(row => {
            const shouldShow = this.selectedStyles.some(selectedStyle => 
                row.estilo.toLowerCase().includes(selectedStyle.toLowerCase())
            );
            row.element.style.display = shouldShow ? '' : 'none';
        });

        this.updateVisibleRowCount();
    }

    applyGeneralSearch(searchTerm) {
        if (!searchTerm) {
            this.showAllRows();
            return;
        }

        this.tableData.forEach(row => {
            const searchableText = [
                row.genero, row.categoria, row.estilo, row.color
            ].join(' ').toLowerCase();
            
            const shouldShow = searchableText.includes(searchTerm.toLowerCase());
            row.element.style.display = shouldShow ? '' : 'none';
        });

        this.updateVisibleRowCount();
    }

    showAllRows() {
        this.tableData.forEach(row => {
            row.element.style.display = '';
        });
        this.updateVisibleRowCount();
    }

    setupTableSorting() {
        const headers = document.querySelectorAll('#dataTable th');
        headers.forEach((header, index) => {
            if (index < 5) {
                header.style.cursor = 'pointer';
                header.addEventListener('click', () => {
                    this.sortTable(index);
                });
                header.innerHTML += ' <span class="sort-indicator"></span>';
            }
        });
    }

    sortTable(columnIndex) {
        const newDirection = (this.sortColumn === columnIndex && this.sortDirection === 'asc') ? 'desc' : 'asc';
        
        this.sortColumn = columnIndex;
        this.sortDirection = newDirection;

        document.querySelectorAll('.sort-indicator').forEach(indicator => {
            indicator.textContent = '';
        });
        
        const currentIndicator = document.querySelectorAll('.sort-indicator')[columnIndex];
        if (currentIndicator) {
            currentIndicator.textContent = newDirection === 'asc' ? '↑' : '↓';
        }

        this.tableData.sort((a, b) => {
            let aValue, bValue;
            
            switch(columnIndex) {
                case 0: aValue = a.genero; bValue = b.genero; break;
                case 1: aValue = a.categoria; bValue = b.categoria; break;
                case 2: aValue = a.estilo; bValue = b.estilo; break;
                case 3: aValue = a.color; bValue = b.color; break;
                case 4: aValue = a.precio; bValue = b.precio; break;
                default: return 0;
            }

            if (typeof aValue === 'string') {
                aValue = aValue.toLowerCase();
                bValue = bValue.toLowerCase();
            }

            if (aValue < bValue) return newDirection === 'asc' ? -1 : 1;
            if (aValue > bValue) return newDirection === 'asc' ? 1 : -1;
            return 0;
        });

        const tbody = document.querySelector('#dataTable tbody');
        this.tableData.forEach(row => {
            tbody.appendChild(row.element);
        });
    }

    setupPagination() {
        this.createPaginationControls();
        this.updatePagination();
    }

    createPaginationControls() {
        const container = document.querySelector('.table-container');
        if (!container) return;
        
        const paginationHtml = `
            <div class="pagination-container" style="display: flex; justify-content: space-between; align-items: center; padding: 15px; background: var(--bg-tertiary); border-top: 1px solid var(--border-color);">
                <div class="pagination-info">
                    Mostrando <span id="currentRange">1-50</span> de <span id="totalItems">${this.tableData.length}</span> productos
                </div>
                <div class="pagination-controls">
                    <button id="prevPage" class="btn btn-outline" style="margin-right: 10px;">Anterior</button>
                    <span id="pageNumbers"></span>
                    <button id="nextPage" class="btn btn-outline" style="margin-left: 10px;">Siguiente</button>
                </div>
                <div class="items-per-page">
                    <select id="itemsPerPage" style="padding: 5px; border-radius: 4px; border: 1px solid var(--border-color);">
                        <option value="25">25 por página</option>
                        <option value="50" selected>50 por página</option>
                        <option value="100">100 por página</option>
                        <option value="all">Todos</option>
                    </select>
                </div>
            </div>
        `;
        
        container.insertAdjacentHTML('beforeend', paginationHtml);

        const prevBtn = document.getElementById('prevPage');
        const nextBtn = document.getElementById('nextPage');
        const itemsSelect = document.getElementById('itemsPerPage');
        
        if (prevBtn) prevBtn.addEventListener('click', () => this.previousPage());
        if (nextBtn) nextBtn.addEventListener('click', () => this.nextPage());
        if (itemsSelect) {
            itemsSelect.addEventListener('change', (e) => {
                this.itemsPerPage = e.target.value === 'all' ? this.tableData.length : parseInt(e.target.value);
                this.currentPage = 1;
                this.updatePagination();
            });
        }
    }

    updatePagination() {
        const visibleRows = this.tableData.filter(row => row.element.style.display !== 'none');
        const totalPages = this.itemsPerPage === this.tableData.length ? 1 : Math.ceil(visibleRows.length / this.itemsPerPage);
        
        if (this.itemsPerPage !== this.tableData.length) {
            const startIndex = (this.currentPage - 1) * this.itemsPerPage;
            const endIndex = startIndex + this.itemsPerPage;
            
            visibleRows.forEach((row, index) => {
                row.element.style.display = (index >= startIndex && index < endIndex) ? '' : 'none';
            });
        }

        const startItem = Math.min((this.currentPage - 1) * this.itemsPerPage + 1, visibleRows.length);
        const endItem = Math.min(this.currentPage * this.itemsPerPage, visibleRows.length);
        
        const currentRangeEl = document.getElementById('currentRange');
        const totalItemsEl = document.getElementById('totalItems');
        const prevBtn = document.getElementById('prevPage');
        const nextBtn = document.getElementById('nextPage');
        
        if (currentRangeEl) currentRangeEl.textContent = `${startItem}-${endItem}`;
        if (totalItemsEl) totalItemsEl.textContent = visibleRows.length;
        if (prevBtn) prevBtn.disabled = this.currentPage === 1;
        if (nextBtn) nextBtn.disabled = this.currentPage === totalPages;

        this.generatePageNumbers(totalPages);
    }

    generatePageNumbers(totalPages) {
        const pageNumbers = document.getElementById('pageNumbers');
        if (!pageNumbers) return;
        
        let html = '';
        const maxVisiblePages = 5;
        let startPage = Math.max(1, this.currentPage - Math.floor(maxVisiblePages / 2));
        let endPage = Math.min(totalPages, startPage + maxVisiblePages - 1);
        
        if (endPage - startPage < maxVisiblePages - 1) {
            startPage = Math.max(1, endPage - maxVisiblePages + 1);
        }

        for (let i = startPage; i <= endPage; i++) {
            html += `<button class="page-number ${i === this.currentPage ? 'active' : ''}" 
                            onclick="inventorySystem.goToPage(${i})" 
                            style="margin: 0 2px; padding: 5px 10px; border: 1px solid var(--border-color); background: ${i === this.currentPage ? 'var(--primary-color)' : 'white'}; color: ${i === this.currentPage ? 'white' : 'var(--text-primary)'}; border-radius: 4px; cursor: pointer;">
                        ${i}
                    </button>`;
        }
        
        pageNumbers.innerHTML = html;
    }

    previousPage() {
        if (this.currentPage > 1) {
            this.currentPage--;
            this.updatePagination();
        }
    }

    nextPage() {
        const visibleRows = this.tableData.filter(row => row.element.style.display !== 'none');
        const totalPages = Math.ceil(visibleRows.length / this.itemsPerPage);
        
        if (this.currentPage < totalPages) {
            this.currentPage++;
            this.updatePagination();
        }
    }

    goToPage(page) {
        this.currentPage = page;
        this.updatePagination();
    }

    setupAdvancedFilters() {
        this.createAdvancedFilterPanel();
    }

    createAdvancedFilterPanel() {
        const controlsSection = document.querySelector('.controls-section');
        if (!controlsSection) return;
        
        const filterPanelHtml = `
            <div class="advanced-filters" style="width: 100%; margin-top: 15px; background: var(--bg-primary); border-radius: var(--radius-lg); padding: 20px; box-shadow: var(--shadow-md); border: 1px solid var(--border-color); display: none;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                    <h3 style="margin: 0; color: var(--primary-color);"><i class="fas fa-filter"></i> Filtros Avanzados</h3>
                    <button id="clearFilters" class="btn btn-outline" style="padding: 5px 10px;">Limpiar Filtros</button>
                </div>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 500;">Rango de Precio:</label>
                        <div style="display: flex; gap: 10px;">
                            <input type="number" id="minPrice" placeholder="Mín" style="flex: 1; padding: 8px; border: 1px solid var(--border-color); border-radius: 4px;">
                            <input type="number" id="maxPrice" placeholder="Máx" style="flex: 1; padding: 8px; border: 1px solid var(--border-color); border-radius: 4px;">
                        </div>
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 500;">Rotación Mínima:</label>
                        <input type="number" id="minRotacion" step="0.1" placeholder="0.0" style="width: 100%; padding: 8px; border: 1px solid var(--border-color); border-radius: 4px;">
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 500;">Estado de Stock:</label>
                        <select id="stockStatus" style="width: 100%; padding: 8px; border: 1px solid var(--border-color); border-radius: 4px;">
                            <option value="">Todos</option>
                            <option value="low">Bajo Stock</option>
                            <option value="normal">Stock Normal</option>
                            <option value="high">Alto Stock</option>
                        </select>
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 500;">Género:</label>
                        <select id="generoFilter" style="width: 100%; padding: 8px; border: 1px solid var(--border-color); border-radius: 4px;">
                            <option value="">Todos los géneros</option>
                        </select>
                    </div>
                </div>
            </div>
        `;
        
        controlsSection.insertAdjacentHTML('afterend', filterPanelHtml);

        const controlsRight = document.querySelector('.controls-right');
        if (controlsRight) {
            const toggleButton = document.createElement('button');
            toggleButton.className = 'btn btn-outline';
            toggleButton.innerHTML = '<i class="fas fa-filter"></i> Filtros Avanzados';
            toggleButton.addEventListener('click', this.toggleAdvancedFilters);
            controlsRight.appendChild(toggleButton);
        }

        this.populateGeneroFilter();

        const minPrice = document.getElementById('minPrice');
        const maxPrice = document.getElementById('maxPrice');
        const minRotacion = document.getElementById('minRotacion');
        const stockStatus = document.getElementById('stockStatus');
        const generoFilter = document.getElementById('generoFilter');
        const clearFilters = document.getElementById('clearFilters');

        if (minPrice) minPrice.addEventListener('input', () => this.applyAdvancedFilters());
        if (maxPrice) maxPrice.addEventListener('input', () => this.applyAdvancedFilters());
        if (minRotacion) minRotacion.addEventListener('input', () => this.applyAdvancedFilters());
        if (stockStatus) stockStatus.addEventListener('change', () => this.applyAdvancedFilters());
        if (generoFilter) generoFilter.addEventListener('change', () => this.applyAdvancedFilters());
        if (clearFilters) clearFilters.addEventListener('click', () => this.clearAdvancedFilters());
    }

    populateGeneroFilter() {
        const generos = [...new Set(this.tableData.map(row => row.genero).filter(g => g))];
        const select = document.getElementById('generoFilter');
        
        if (!select) return;
        
        generos.forEach(genero => {
            const option = document.createElement('option');
            option.value = genero;
            option.textContent = genero;
            select.appendChild(option);
        });
    }

    toggleAdvancedFilters() {
        const panel = document.querySelector('.advanced-filters');
        if (panel) {
            panel.style.display = panel.style.display === 'none' ? 'block' : 'none';
        }
    }

    setupEventListeners() {
        // Configurar eventos adicionales si es necesario
    }

    // Utilidades básicas
    parseNumber(str) {
        return parseInt(str.toString().replace(/[^\d]/g, '')) || 0;
    }

    parseFloat(str) {
        return parseFloat(str.toString().replace(/[^\d.-]/g, '')) || 0;
    }

    parsePrice(str) {
        return parseFloat(str.toString().replace(/[^\d.-]/g, '')) || 0;
    }

    formatCurrency(value) {
        return new Intl.NumberFormat('es-GT', {
            style: 'currency',
            currency: 'GTQ',
            minimumFractionDigits: 2
        }).format(value);
    }

    formatNumber(value) {
        return new Intl.NumberFormat('es-GT').format(value);
    }
}


/**
 * Sistema Avanzado de Inventario con Rotación - PARTE 2
 * Métricas, exportaciones y funcionalidades avanzadas
 */

// CONTINUACIÓN DE LA CLASE InventorySystem

InventorySystem.prototype.applyAdvancedFilters = function() {
    const minPriceEl = document.getElementById('minPrice');
    const maxPriceEl = document.getElementById('maxPrice');
    const minRotacionEl = document.getElementById('minRotacion');
    const stockStatusEl = document.getElementById('stockStatus');
    const generoFilterEl = document.getElementById('generoFilter');

    const minPrice = minPriceEl ? parseFloat(minPriceEl.value) || 0 : 0;
    const maxPrice = maxPriceEl ? parseFloat(maxPriceEl.value) || Infinity : Infinity;
    const minRotacion = minRotacionEl ? parseFloat(minRotacionEl.value) || 0 : 0;
    const stockStatus = stockStatusEl ? stockStatusEl.value : '';
    const generoFilter = generoFilterEl ? generoFilterEl.value : '';

    this.tableData.forEach(row => {
        let shouldShow = true;

        if (row.precio < minPrice || row.precio > maxPrice) {
            shouldShow = false;
        }

        const maxRotacion = Math.max(...Object.values(row.rotaciones));
        if (maxRotacion < minRotacion) {
            shouldShow = false;
        }

        if (generoFilter && row.genero !== generoFilter) {
            shouldShow = false;
        }

        if (stockStatus) {
            const totalVentas = Object.values(row.ventas).reduce((sum, val) => sum + val, 0);
            const totalStock = Object.values(row.stocks).reduce((sum, val) => sum + val, 0);
            
            switch(stockStatus) {
                case 'low':
                    if (totalStock >= totalVentas || totalVentas === 0) shouldShow = false;
                    break;
                case 'high':
                    if (totalStock <= totalVentas * 2 || totalVentas === 0) shouldShow = false;
                    break;
                case 'normal':
                    if (totalStock < totalVentas || totalStock > totalVentas * 2 || totalVentas === 0) shouldShow = false;
                    break;
            }
        }

        row.element.style.display = shouldShow ? '' : 'none';
    });

    this.updateVisibleRowCount();
    this.updatePagination();
};

InventorySystem.prototype.clearAdvancedFilters = function() {
    const elements = ['minPrice', 'maxPrice', 'minRotacion', 'stockStatus', 'generoFilter'];
    elements.forEach(id => {
        const el = document.getElementById(id);
        if (el) el.value = '';
    });
    
    this.showAllRows();
};

InventorySystem.prototype.setupTooltips = function() {
    document.querySelectorAll('.rotacion-column').forEach(cell => {
        const value = parseFloat(cell.textContent);
        let tooltip = '';
        
        if (value >= 4) {
            tooltip = 'Alta rotación: Producto muy demandado';
        } else if (value >= 2) {
            tooltip = 'Rotación moderada: Ventas constantes';
        } else if (value > 0) {
            tooltip = 'Baja rotación: Considerar promociones';
        } else {
            tooltip = 'Sin rotación: Producto sin ventas';
        }
        
        cell.setAttribute('title', tooltip);
        cell.classList.add('tooltip');
    });

    document.querySelectorAll('.low-stock, .high-stock').forEach(cell => {
        if (cell.classList.contains('low-stock')) {
            cell.setAttribute('title', 'Alerta: Stock menor que ventas');
        } else {
            cell.setAttribute('title', 'Stock alto: Más del doble de las ventas');
        }
    });
};

InventorySystem.prototype.setupKeyboardShortcuts = function() {
    document.addEventListener('keydown', (e) => {
        if ((e.ctrlKey || e.metaKey) && e.key === 'f') {
            e.preventDefault();
            const searchInput = document.getElementById('searchInput');
            if (searchInput) searchInput.focus();
        }
        
        if ((e.ctrlKey || e.metaKey) && e.key === 'e') {
            e.preventDefault();
            this.exportToExcel();
        }
        
        if ((e.ctrlKey || e.metaKey) && e.key === 'p') {
            e.preventDefault();
            this.exportToPDF();
        }
        
        if (e.key === 'Escape') {
            this.clearSearch();
        }
    });
};

InventorySystem.prototype.calculateAdvancedMetrics = function() {
    this.createMetricsPanel();
};

InventorySystem.prototype.createMetricsPanel = function() {
    const summarySection = document.querySelector('.summary-section');
    if (!summarySection) return;
    
    const metricsHtml = `
        <div class="advanced-metrics" style="margin-top: 25px; background: var(--bg-primary); border-radius: var(--radius-lg); padding: 20px; box-shadow: var(--shadow-md); border: 1px solid var(--border-color);">
            <h3 style="margin: 0 0 15px 0; color: var(--primary-color);"><i class="fas fa-chart-bar"></i> Métricas Avanzadas</h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                <div class="metric-item">
                    <div class="metric-label">Productos Alta Rotación</div>
                    <div class="metric-value" id="highRotationCount">-</div>
                    <div class="metric-subtitle">Rotación ≥ 4</div>
                </div>
                <div class="metric-item">
                    <div class="metric-label">Valor Total Inventario</div>
                    <div class="metric-value" id="totalInventoryValue">-</div>
                    <div class="metric-subtitle">Precio × Stock</div>
                </div>
                <div class="metric-item">
                    <div class="metric-label">Eficiencia Promedio</div>
                    <div class="metric-value" id="averageEfficiency">-</div>
                    <div class="metric-subtitle">Ventas/Stock ratio</div>
                </div>
                <div class="metric-item">
                    <div class="metric-label">Productos Sin Movimiento</div