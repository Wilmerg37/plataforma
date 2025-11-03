// Funcionalidad del Sidebar Responsivo
$(document).ready(function() {
    
    // Manejar el toggle del sidebar
    $('[data-widget="pushmenu"]').on('click', function(e) {
        e.preventDefault();
        
        if ($(window).width() > 991) {
            // Desktop: colapsar/expandir
            $('body').toggleClass('sidebar-collapse');
            
            // Guardar estado en localStorage
            if ($('body').hasClass('sidebar-collapse')) {
                localStorage.setItem('sidebar-state', 'collapsed');
            } else {
                localStorage.setItem('sidebar-state', 'expanded');
            }
        } else {
            // Móvil: mostrar/ocultar
            $('body').toggleClass('sidebar-open');
        }
    });
    
    // Restaurar estado del sidebar al cargar la página
    $(window).on('load', function() {
        if ($(window).width() > 991) {
            const sidebarState = localStorage.getItem('sidebar-state');
            if (sidebarState === 'collapsed') {
                $('body').addClass('sidebar-collapse');
            }
        }
    });
    
    // Manejar redimensionamiento de ventana
    $(window).on('resize', function() {
        if ($(window).width() > 991) {
            // Desktop: remover clase sidebar-open y restaurar estado collapse
            $('body').removeClass('sidebar-open');
            
            const sidebarState = localStorage.getItem('sidebar-state');
            if (sidebarState === 'collapsed') {
                $('body').addClass('sidebar-collapse');
            } else {
                $('body').removeClass('sidebar-collapse');
            }
        } else {
            // Móvil: remover clase sidebar-collapse
            $('body').removeClass('sidebar-collapse');
        }
    });
    
    // Cerrar sidebar en móvil al hacer clic en overlay
    $(document).on('click', function(e) {
        if ($(window).width() <= 991 && $('body').hasClass('sidebar-open')) {
            if (!$(e.target).closest('.main-sidebar, [data-widget="pushmenu"]').length) {
                $('body').removeClass('sidebar-open');
            }
        }
    });
    
    // Agregar tooltips para sidebar colapsado
    function updateTooltips() {
        if ($('body').hasClass('sidebar-collapse')) {
            $('.nav-sidebar .nav-link').each(function() {
                const text = $(this).find('p').text().trim();
                if (text) {
                    $(this).attr('data-tooltip', text);
                }
            });
        } else {
            $('.nav-sidebar .nav-link').removeAttr('data-tooltip');
        }
    }
    
    // Actualizar tooltips cuando cambie el estado
    $('body').on('transitionend', function() {
        updateTooltips();
    });
    
    // Manejar navegación del sidebar
    $('.nav-sidebar .nav-link').on('click', function(e) {
        const $this = $(this);
        const $parent = $this.parent();
        
        // Si es un enlace con submenu
        if ($parent.hasClass('has-treeview')) {
            e.preventDefault();
            
            // Toggle del submenu actual
            $parent.toggleClass('menu-open');
            
            // Cerrar otros submenus
            $parent.siblings('.has-treeview').removeClass('menu-open');
            
            // En móvil, mantener el sidebar abierto
            if ($(window).width() <= 991) {
                // No cerrar el sidebar
                return false;
            }
        } else {
            // Es un enlace directo - cerrar sidebar en móvil
            if ($(window).width() <= 991) {
                setTimeout(function() {
                    $('body').removeClass('sidebar-open');
                }, 100);
            }
        }
    });
    
    // Smooth scrolling para navegación interna
    $('a[href^="#"]').on('click', function(e) {
        e.preventDefault();
        const target = $(this.hash);
        if (target.length) {
            $('html, body').animate({
                scrollTop: target.offset().top - 80
            }, 500);
        }
    });
    
    // Indicador de página activa
    function setActiveNavItem() {
        const currentPage = window.location.pathname.split('/').pop();
        
        $('.nav-sidebar .nav-link').removeClass('active');
        $('.nav-sidebar .nav-link[href="' + currentPage + '"]').addClass('active');
        
        // Si el enlace activo está en un submenu, abrir el submenu
        const $activeLink = $('.nav-sidebar .nav-link.active');
        const $parentTreeview = $activeLink.closest('.has-treeview');
        
        if ($parentTreeview.length) {
            $parentTreeview.addClass('menu-open');
        }
    }
    
    // Establecer elemento activo al cargar
    setActiveNavItem();
    
    // Animación suave para las transiciones
    $('.main-sidebar, .main-header, .content-wrapper, .main-footer').css({
        'transition': 'all 0.3s ease-in-out'
    });
    
    // Función para ajustar el layout después de cambios
    function adjustLayout() {
        // Trigger resize event para otros plugins que puedan necesitar reajustarse
        setTimeout(function() {
            $(window).trigger('resize');
        }, 350);
    }
    
    // Llamar adjustLayout después de cambios en el sidebar
    $('[data-widget="pushmenu"]').on('click', function() {
        adjustLayout();
    });
    
    // Mejorar experiencia en tablets
    if ($(window).width() <= 991 && $(window).width() > 768) {
        $('body').addClass('sidebar-mini');
    }
    
    // Prevenir scroll del body cuando el sidebar está abierto en móvil
    $('body').on('sidebar-open sidebar-close', function() {
        if ($(window).width() <= 991) {
            if ($('body').hasClass('sidebar-open')) {
                $('body').css('overflow', 'hidden');
            } else {
                $('body').css('overflow', '');
            }
        }
    });
    
    // Trigger eventos personalizados
    $('[data-widget="pushmenu"]').on('click', function() {
        if ($(window).width() <= 991) {
            if ($('body').hasClass('sidebar-open')) {
                $('body').trigger('sidebar-close').removeClass('sidebar-open');
            } else {
                $('body').trigger('sidebar-open').addClass('sidebar-open');
            }
        }
    });
});