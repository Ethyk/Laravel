<script lang="ts">
  import PlaceholderPattern from '@/components/PlaceholderPattern.svelte';
  import AppLayout from '@/layouts/AppLayout.svelte';
  import { type BreadcrumbItem } from '@/types';

  import { onMount } from 'svelte';
  // import { page } from '$app/stores';

  import { Link, page } from '@inertiajs/svelte';

  let user = $derived($page.props.auth.user);
  let disponibilites = $derived($page.props.disponibilites|| null);
  

  // Accès aux props envoyées depuis Laravel
  // let tatoueurs = $page.props.tatoueurs || [];
  let tatoueur = $derived($page.props.tatoueur|| null);
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
        <div class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border flex justify-center items-center">
          <!-- <a href="/salon/create" class="z-10" use:inertia>nouveau salon</a> -->
          {#if tatoueur}
          <Link href="/tatoueurs/{tatoueur.id}"
          class="z-10 inline-block rounded-sm border border-[#19140035] px-5 py-1.5 text-sm leading-normal text-[#1b1b18] hover:border-[#1915014a] dark:border-[#3E3E3A] dark:text-[#EDEDEC] dark:hover:border-[#62605b]"
          method="get" as="button">Editer le profile  {tatoueur.bio}</Link>
      {:else}
        <Link href="/tatoueurs/create"
        class="z-10 inline-block rounded-sm border border-[#19140035] px-5 py-1.5 text-sm leading-normal text-[#1b1b18] hover:border-[#1915014a] dark:border-[#3E3E3A] dark:text-[#EDEDEC] dark:hover:border-[#62605b]"
        method="get" as="button">nouveau profile Tattoueur</Link>
      {/if}
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
            <h1>Info du tatoueur</h1>
            {#if status}
              <div class="alert">{status}</div>
            {/if}
            <ul>
              {#if tatoueur}
                <li>
                  {user.name} {user.name}
                  {#if tatoueur.bio}
                    <div class="salons">
                      bio :        //  dd($tatoueur ?? [] ,);

                          <!-- <span>{JSON.stringify(disponibilites.disponibilites)} </span> -->

                      <!-- {#each tatoueur.salons as salon} -->
                        <!-- <span>{tatoueur.instagram}</span>
                        <!-- <span>{tatoueur.bio}</span> -->
                        <!-- <span>{tatoueur.disponibilites}</span> -->
                        {#each  disponibilites.disponibilites as dispo}
                        <!-- <span>{dispo.jour} </span> -->
                        <!-- <span>{disponibilites}</span> -->
                        <h3>{dispo.jour}</h3>
                        <!-- <h3>{dispo.plages_horaires}</h3> -->
                          <!-- <span>{JSON.stringify(dispo.plages_horaires)} </span> -->
                          {#each  dispo.plages_horaires as horaire, index}
                            {#if index === 0}
                                <span>Matin</span>
                            {:else if index === 1}
                                <span>Après-midi</span>
                            {/if}
                          <span>De {horaire.debut} à {horaire.fin}</span>
                              <span></span>
                              <!-- <span>{JSON.stringify(dispo)} </span> -->
                          {/each}
                        
                      {/each}
                    </div>
                  {/if}
                </li>
              {/if}
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


