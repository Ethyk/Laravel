<script lang="ts">
    import PlaceholderPattern from '@/components/PlaceholderPattern.svelte';
    import AppLayout from '@/layouts/AppLayout.svelte';
    import { type BreadcrumbItem } from '@/types';
  
    import { onMount } from 'svelte';
    // import { page } from '$app/stores';
  
    import { Link, page } from '@inertiajs/svelte';
  
    let user = $derived($page.props.auth.user);
  
    // Accès aux props envoyées depuis Laravel
    // let tatoueurs = $page.props.tatoueurs || [];
    // let tatoueurs = $derived($page.props.tatoueurs|| []);
    let salons = $derived($page.props.salons|| []); 
  
    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Dashboard',
            href: '/dashboard',
        },
    ];
  </script>
  
  <svelte:head>
    <title>Dashboard</title>
  </svelte:head>
  
  <AppLayout {breadcrumbs}>
    <div class="space-y-4 px-4 pt-4">
        <div class="grid auto-rows-min gap-4 md:grid-cols-3">
            <div class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                <PlaceholderPattern class="absolute inset-0 size-full stroke-neutral-900/20 dark:stroke-neutral-100/20" />
            </div>
            <div class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                <PlaceholderPattern class="absolute inset-0 size-full stroke-neutral-900/20 dark:stroke-neutral-100/20" />
            </div>
            <div class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                <PlaceholderPattern class="absolute inset-0 size-full stroke-neutral-900/20 dark:stroke-neutral-100/20" />
            </div>
        </div>
        <div class="relative h-[calc(100vh-21rem)] overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
            <PlaceholderPattern class="absolute inset-0 size-full stroke-neutral-900/20 dark:stroke-neutral-100/20" />
            <div>
              <h1>Liste des tatoueurs</h1>
              {#if status}
                <div class="alert">{status}</div>
              {/if}
              <ul>
                {#each salons as salon}
                  <li>
                    <!-- {JSON.stringify(tatoueur, null, 2)} -->
                    {user.name} {user.name}
                    {#if salon.description}
                    <div class="salons">
                        <span>{salon.description}</span>
                    </div>
                    {/if}
                  </li>
                {/each}
              </ul>
            </div>
        </div>
    </div>
  
  </AppLayout>
  
  
  
  
  <style>
    .alert {
      color: green;
      margin-bottom: 1rem;
    }
    ul {
      list-style: none;
      padding: 0;
    }
    li {
      margin-bottom: 1rem;
      padding: 1rem;
      border: 1px solid #eee;
    }
    .salons {
      margin-top: 0.5rem;
      font-size: 0.9em;
      color: #666;
    }
  </style>