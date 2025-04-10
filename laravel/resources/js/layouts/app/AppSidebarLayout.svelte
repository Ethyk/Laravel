<script lang="ts">
    import AppContent from '@/components/AppContent.svelte';
    import AppShell from '@/components/AppShell.svelte';
    import AppSidebar from '@/components/AppSidebar.svelte';
    import AppSidebarHeader from '@/components/AppSidebarHeader.svelte';
    import type { BreadcrumbItemType } from '@/types';
    import type { Snippet } from 'svelte';
	import { Toaster, toast } from 'svelte-sonner';

    interface Props {
        breadcrumbs?: BreadcrumbItemType[];
        children?: Snippet;
        flash?: any;
    }

    let { breadcrumbs = [], children, flash = {} }: Props = $props();
     // Afficher les messages flash au chargement
     
    //  $effect(() => {
    //      if (flash.success) {
    //          toast.success(flash.success); // Message de succès
    //         }
            
    //         if (flash.error) {
    //             toast.error(flash.error); // Message d'erreur
    //         }
    //         console.log(flash);
    // });

</script>

<AppShell variant="sidebar">
    <AppSidebar />
    <AppContent variant="sidebar">
        <AppSidebarHeader {breadcrumbs} />
        {@render children?.()}
        <Toaster richColors/>
    </AppContent>
</AppShell>
