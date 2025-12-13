import axios from 'axios';

/**
 * Composable para tracking de analytics
 */
export function useAnalytics() {
    /**
     * Trackear un evento
     */
    const track = (eventType, eventName, options = {}) => {
        const {
            section = null,
            targetUrl = null,
            metadata = null,
        } = options;

        // Enviar a Google Analytics 4 si está configurado
        if (window.gtag) {
            window.gtag('event', eventName, {
                event_category: section || 'general',
                event_label: eventName,
                value: 1,
                ...metadata,
            });
        }

        // Enviar a nuestro backend usando axios (no Inertia)
        axios.post('/api/analytics/track', {
            event_type: eventType,
            event_name: eventName,
            section: section,
            target_url: targetUrl,
            metadata: metadata,
        }).catch(error => {
            // Silenciar errores de tracking para no afectar la experiencia del usuario
            console.warn('Error tracking analytics:', error);
        });
    };

    /**
     * Trackear una vista de página
     */
    const trackPageView = (pageName = 'home') => {
        track('page_view', 'page_view', {
            section: 'page',
            metadata: { page: pageName },
        });
    };

    /**
     * Trackear un click en un link
     */
    const trackLinkClick = (linkName, url, section = null) => {
        track('link_click', linkName, {
            section: section,
            targetUrl: url,
            metadata: { link_name: linkName },
        });
    };

    /**
     * Trackear un click en un botón
     */
    const trackButtonClick = (buttonName, section = null, metadata = null) => {
        track('click', buttonName, {
            section: section,
            metadata: metadata,
        });
    };

    /**
     * Trackear una descarga
     */
    const trackDownload = (fileName, fileUrl, section = null) => {
        track('download', fileName, {
            section: section,
            targetUrl: fileUrl,
            metadata: { file_name: fileName },
        });
    };

    /**
     * Trackear click en menú de navegación
     */
    const trackMenuClick = (menuItem) => {
        trackButtonClick(`menu_${menuItem}`, 'navbar', { menu_item: menuItem });
    };

    /**
     * Trackear click en proyecto
     */
    const trackProjectClick = (projectName, linkType, url) => {
        trackLinkClick(`project_${linkType}`, url, 'projects', {
            project_name: projectName,
            link_type: linkType, // 'demo' o 'github'
        });
    };

    /**
     * Trackear click en contacto
     */
    const trackContactClick = (contactType, url) => {
        trackLinkClick(`contact_${contactType}`, url, 'contact', {
            contact_type: contactType, // 'email', 'linkedin', 'github', 'whatsapp'
        });
    };

    /**
     * Trackear visualización de CV
     */
    const trackCvView = () => {
        trackButtonClick('cv_view', 'cv_modal', { action: 'view' });
    };

    /**
     * Trackear descarga de CV
     */
    const trackCvDownload = (cvUrl) => {
        trackDownload('cv_download', cvUrl, 'cv_modal');
    };

    return {
        track,
        trackPageView,
        trackLinkClick,
        trackButtonClick,
        trackDownload,
        trackMenuClick,
        trackProjectClick,
        trackContactClick,
        trackCvView,
        trackCvDownload,
    };
}

