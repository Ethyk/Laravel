<script lang="ts">
  import PlaceholderPattern from '@/components/PlaceholderPattern.svelte';
  import AppLayout from '@/layouts/AppLayout.svelte';
  import { type BreadcrumbItem } from '@/types';
  import { router, useForm } from '@inertiajs/svelte';

  import { onMount } from 'svelte';
  // import { page } from '$app/stores';

  import { Link, page } from '@inertiajs/svelte';
  import { Button } from '@/components/ui/button';
    import {
        Dialog,
        DialogClose,
        DialogContent,
        DialogDescription,
        DialogFooter,
        DialogHeader,
        DialogTitle,
        DialogTrigger,
    } from '@/components/ui/dialog';
    import Label from '@/components/ui/label/label.svelte';
    import Input from '@/components/ui/input/input.svelte';
    import InputError from '@/components/InputError.svelte';
  let user = $derived($page.props.auth.user);
  let disponibilites = $derived($page.props.disponibilites|| null);
  

  // Accès aux props envoyées depuis Laravel
  // let tatoueurs = $page.props.tatoueurs || [];
  let tatoueur = $derived($page.props.tatoueur|| null);
  let salons = $derived($page.props.salons|| []); 
  console.log(tatoueur);

  const breadcrumbs: BreadcrumbItem[] = [
      {
          title: 'Dashboard',
          href: '/dashboard',
      },
  ];


  const form = useForm({
        ...tatoueur,
    });
  
    const deleteUser = (e: Event) => {
      console.log("dsadas");
        e.preventDefault();

        $form.delete(route('profile.destroy'), {
            preserveScroll: true,
            onSuccess: () => closeModal(),
            onError: () => passwordInput?.focus(),
            onFinish: () => $form.reset(),
        });
    };

    const closeModal = () => {
        $form.clearErrors();
        $form.reset();
    };
    // const findIndex = (id) => {
    //     return tatoueur.contracted_salons.findIndex(salon => salon.id === id);
    // };
    const findIndex = (id, type = 'contracted_salons') => {
    const dataset = tatoueur[type]; // Dynamically select the dataset based on `type`
    if (!dataset || !Array.isArray(dataset)) {
        console.error(`Invalid dataset: ${type}`);
        return -1; // Return -1 if the dataset doesn't exist or isn't an array
    }
    return dataset.findIndex(salon => salon.id === id); // Find index based on `id`
};
    // EditSalon
    const EditSalon = (e, id) => {
        e.preventDefault();

         // Get the updated salon data
        let updatedSalon = tatoueur.salons.find(salon => salon.id === id);
        console.log(updatedSalon);
        router.patch(route('salons.update', updatedSalon), {
            preserveScroll: true,
        });
    };
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
      <div class="relative h-[calc(100vh-21rem)] overflow-auto rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
      <!-- <div class="relative h-[calc(100vh-21rem)] overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border"> -->
          <PlaceholderPattern     class="absolute inset-0 size-full stroke-neutral-900/20 dark:stroke-neutral-100/20 pointer-events-none" />
          <div>
            <h1>Mes constract en cours : {tatoueur.contracted_salons.length}</h1>
            {#if status}
              <div class="alert">{status}</div>
            {/if}
            <ul>
                       <!-- <span>{JSON.stringify(tatoueur)} </span> -->

              {#if tatoueur?.contracted_salons}
              {#each tatoueur.contracted_salons as salon (salon.id)}
                <li >
                    {salon.name} {salon.pays}
                    <!-- <div class="salons"> -->
                      <Link href="/salons/{salon.id}"
                      class="z-10 "
                      method="get" >
                      <div class="salons bg-gradient-to-r from-yellow-200 via-gray-500 to-white-500 text-white font-bold text-center p-6 rounded-lg shadow-xl border-2 border-gray-200 hover:shadow-2xl transform hover:scale-105 transition-all duration-300">
                       <!-- <span>{JSON.stringify(disponibilites.disponibilites)} </span> -->
                        <span>Salon : {salon.description}</span>
                      </div>
                    </Link>
                    <Dialog  class="z-10 ">
                      <DialogTrigger  >
                          <Button variant="destructive">Delete account</Button>
                      </DialogTrigger>
                      <DialogContent>
                          <form class="space-y-6" onsubmit={deleteUser}>
                              <DialogHeader class="space-y-3">
                                  <DialogTitle>Are you sure you want to delete your account?</DialogTitle>
                                  <DialogDescription>
                                      Once your account is deleted, all of its resources and data will also be permanently deleted. Please enter your password
                                      to confirm you would like to permanently delete your account.
                                  </DialogDescription>
                              </DialogHeader>
          
                              <div class="grid gap-2">
                                  <Label for={`name-${salon.id}`}  class="sr-only">Bio</Label>
                                  <Input id={`name-${salon.id}`} type="text" name={`bio-${salon.id}`} bind:value={tatoueur.contracted_salons[findIndex(salon.id)].name} placeholder={`${salon.name}`} />

                                  <!-- <InputError message={tatoueur.contracted_salons[findIndex(salon.id)].errors.bio} /> -->
                              </div>
          
                              <DialogFooter class="gap-2">
                                  <DialogClose>
                                      <Button variant="secondary" onclick={closeModal}>Cancel</Button>
                                  </DialogClose>
          
                                  <Button variant="destructive" disabled={$form.processing}>
                                      <button type="submit">Delete account</button>
                                  </Button>
                              </DialogFooter>
                          </form>
                      </DialogContent>
                  </Dialog>
                </li>
              {/each}
              {/if}
            </ul>
          </div>

          <div>
            <h1>Mes Salons : {tatoueur.salons.length}</h1>
            {#if status}
              <div class="alert">{status}</div>
            {/if}
            <ul>
              {#if tatoueur?.salons}
              {#each tatoueur.salons as salon (salon.id)} 
                <li>
                    {salon.name} {salon.pays}
                    <div class="salons">
                       <!-- <span>{JSON.stringify(disponibilites.disponibilites)} </span> -->
                       <span>Salon : {salon.description}</span>
                       
                    </div>
                    <Dialog  class="">
                      <DialogTrigger  >
                          <Button variant="destructive">Edit Salon Details</Button>
                      </DialogTrigger>
                      <DialogContent>
                        <form class="space-y-6" onsubmit={(e) => EditSalon(e, salon.id)}>
                          <DialogHeader class="space-y-3">
                                  <DialogTitle>Edit Salon Details</DialogTitle>
                                  <DialogDescription>
                                    Use the form below to update the details of the selected salon. 
                                    Make sure all the information is correct before saving your changes. You can update the salon's name, address, and contact details.
                                  </DialogDescription>
                          </DialogHeader>
          
                              <div class="grid gap-2">
                                  <Label for={`name-${salon.id}`}  class="sr-only">name</Label>
                                  <Input id={`name-${salon.id}`} type="text" name={`name-${salon.id}`} bind:value={tatoueur.salons[findIndex(salon.id, "salons")].name} placeholder={`${salon.name}`} />
                                  <!-- <InputError message={tatoueur.contracted_salons[findIndex(salon.id)].errors.bio} /> -->
                              </div>
                              <div class="grid gap-2">
                                  <Label for={`description-${salon.id}`}  class="sr-only">description</Label>
                                  <Input id={`description-${salon.id}`} type="text" name={`description-${salon.id}`} bind:value={tatoueur.salons[findIndex(salon.id, "salons")].description} placeholder={`${salon.description}`} />
                                  <!-- <InputError message={tatoueur.contracted_salons[findIndex(salon.id)].errors.bio} /> -->
                              </div>
                              <div class="grid gap-2">
                                  <Label for={`adresse-${salon.id}`}  class="sr-only">adresse</Label>
                                  <Input id={`adresse-${salon.id}`} type="text" name={`adresse-${salon.id}`} bind:value={tatoueur.salons[findIndex(salon.id, "salons")].adresse} placeholder={`${salon.adresse}`} />
                                  <!-- <InputError message={tatoueur.contracted_salons[findIndex(salon.id)].errors.bio} /> -->
                              </div>
                              <div class="grid gap-2">
                                <Label for={`ville-${salon.id}`}  class="sr-only">ville</Label>
                                <Input id={`ville-${salon.id}`} type="text" name={`ville-${salon.id}`} bind:value={tatoueur.salons[findIndex(salon.id, "salons")].ville} placeholder={`${salon.ville}`} />
                                <!-- <InputError message={tatoueur.contracted_salons[findIndex(salon.id)].errors.bio} /> -->
                              </div>
                              <div class="grid gap-2">
                                <Label for={`code_postal-${salon.id}`}  class="sr-only">code_postal</Label>
                                <Input id={`code_postal-${salon.id}`} type="text" name={`code_postal-${salon.id}`} bind:value={tatoueur.salons[findIndex(salon.id, "salons")].code_postal} placeholder={`${salon.code_postal}`} />
                                <!-- <InputError message={tatoueur.contracted_salons[findIndex(salon.id)].errors.bio} /> -->
                            </div>
                            <div class="grid gap-2">
                              <Label for={`pays-${salon.id}`}  class="sr-only">pays</Label>
                              <Input id={`pays-${salon.id}`} type="text" name={`pays-${salon.id}`} bind:value={tatoueur.salons[findIndex(salon.id, "salons")].pays} placeholder={`${salon.pays}`} />
                              <!-- <InputError message={tatoueur.contracted_salons[findIndex(salon.id)].errors.bio} /> -->
                          </div>
                            <div class="grid gap-2">
                                <Label for={`gestionnaire_id-${salon.id}`}  class="sr-only">gestionnaire_id</Label>
                                <Input id={`gestionnaire_id-${salon.id}`} type="text" name={`gestionnaire_id-${salon.id}`} bind:value={tatoueur.salons[findIndex(salon.id, "salons")].gestionnaire_id} placeholder={`${salon.gestionnaire_id}`} />
                                <!-- <InputError message={tatoueur.contracted_salons[findIndex(salon.id)].errors.bio} /> -->
                            </div>
                        
          
                              <DialogFooter class="gap-2">
                                  <DialogClose>
                                      <Button variant="secondary" onclick={closeModal}>Cancel</Button>
                                  </DialogClose>
          
                                  <Button variant="destructive" disabled={$form.processing}>
                                      <button type="submit">Delete account</button>
                                  </Button>
                              </DialogFooter>
                          </form>
                      </DialogContent>
                  </Dialog>
                </li>
              {/each}
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


