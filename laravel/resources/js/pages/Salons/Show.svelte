<script lang="ts">
    import PlaceholderPattern from '@/components/PlaceholderPattern.svelte';
    import AppLayout from '@/layouts/AppLayout.svelte';
    import { type BreadcrumbItem } from '@/types';
  
    import { onMount } from 'svelte';
    // import { page } from '$app/stores';
  
    import { inertia, Link, page } from '@inertiajs/svelte';
    import TextLink from '@/components/TextLink.svelte';
    import Button from '@/components/ui/button/button.svelte';
  
    let user = $derived($page.props.auth.user);
  
    // Accès aux props envoyées depuis Laravel
    // let tatoueurs = $page.props.tatoueurs || [];
    // let tatoueurs = $derived($page.props.tatoueurs|| []);
    let salon = $derived($page.props.salon|| null); 
  
    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Dashboard',
            href: '/dashboard',
        },
    ];
    // const submit = (e: Event) => {
    //     e.preventDefault();
    //     $form.post(route('login'), {
    //         onFinish: () => $form.reset('password'),
    //     });
    // };
  </script>
  
  <svelte:head>
    <title>Dashboard</title>
  </svelte:head>
  
  <AppLayout {breadcrumbs}>

    <div class="space-y-4 px-4 pt-4">
      
      <div class="grid auto-rows-min gap-4 md:grid-cols-3">
        <!-- <div class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
            <a href="/salon/create" class="absolute z-10" use:inertia>Home</a>
                <PlaceholderPattern class="absolute inset-0 size-full stroke-neutral-900/20 dark:stroke-neutral-100/20" />
        </div> -->
        <div class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border flex justify-center items-center">
          <!-- <a href="/salon/create" class="z-10" use:inertia>nouveau salon</a> -->
    
          

          <PlaceholderPattern class="absolute inset-0 size-full stroke-neutral-900/20 dark:stroke-neutral-100/20" />
      </div>
      

            <div class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                <PlaceholderPattern class="absolute inset-0 size-full stroke-neutral-900/20 dark:stroke-neutral-100/20" />
            </div>
            <div class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                <PlaceholderPattern class="absolute inset-0 size-full stroke-neutral-900/20 dark:stroke-neutral-100/20" />
            </div>
        </div>
        <div class="relative h-[calc(100vh-21rem)] overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border flex justify-center items-center">
        <PlaceholderPattern class="absolute inset-0 size-full stroke-neutral-900/20 dark:stroke-neutral-100/20" />
            <div class="text-center">
              {#if salon}
              <h1 class="text-2xl font-bold mb-6">Détails du Salon</h1>
              <div class="bg-white shadow-md rounded-lg p-6">
                <h2 class="text-xl font-semibold mb-4">{salon.name}</h2>
                <p class="text-gray-600"><strong>Description :</strong> {salon.description}</p>
                <p class="text-gray-600"><strong>Adresse :</strong> {salon.adresse}</p>
                <p class="text-gray-600"><strong>Ville :</strong> {salon.ville}</p>
                <p class="text-gray-600"><strong>Code Postal :</strong> {salon.code_postal}</p>
                <p class="text-gray-600"><strong>Pays :</strong> {salon.pays}</p>
            </div>
        
            <div class="mt-6 relative">
                <a href="/salons" class=" z-10 text-center bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                    Retour à la liste des salons
                </a>

            </div>
              {:else}
                  <p>Vous n avez ou n'apartenez a aucun salon, <br /> la liste est vide.</p>
              {/if}
              <!-- <h1>Liste des tatoueurs</h1>
              {#if status}
                <div class="alert">{status}</div>
              {/if}
              <ul>
                {#each salons as salon}
                  <li> -->
                    <!-- {JSON.stringify(tatoueur, null, 2)} -->
                    <!-- {user.name} {user.name}
                    {#if salon.description}
                    <div class="salons">
                        <span>{salon.description}</span>
                    </div>
                    {/if}
                  </li>
                {/each}
              </ul> -->
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