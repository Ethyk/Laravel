<script lang="ts">
    // import ModelModal from '@/components/ModelModal.svelte';
    import ModelModal from './newModal2.svelte';
    import { useForm, page, Link } from '@inertiajs/svelte';
    import { Input } from '@/components/ui/input';
    import { Label } from '@/components/ui/label';
    import InputError from '@/components/InputError.svelte';
    import AppLayout from '@/layouts/AppLayout.svelte';
    import { type BreadcrumbItem } from '@/types';
    import {  toast } from 'svelte-sonner';

    import {
      Button,
      buttonVariants
    } from "@/components/ui/button/index.js";
    
    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Dashboard',
            href: '/dashboard',
        },
    ];
    let isModalOpen = $state(false);
    let selectedItem = $state(null);

    let { csrf_token, salons, flash} = $props()
  
    const form = useForm({
        id: null,
        name: '',
        description: '',
        adresse: '',
        ville: '',
        code_postal: '',
        pays: '',
        _token: '' //csrf_token
    });
  
    const openModal = (salon = null) => {
      selectedItem = salon;
      $form.reset();
      // console.log("salon",$form.data());
    // console.log("data : ",$form.data.id)

      
      if (salon) {
        // $form.setData({
          $form.id = salon.id;
          $form.name = salon.name;
          $form.description = salon.description;
          $form.adresse = salon.adresse;
          $form.ville = salon.ville;
          $form.code_postal = salon.code_postal;
          $form.pays = salon.pays;
          // });
        }
        $form._token = csrf_token;
        
        isModalOpen = true;
    };
    function delsalon(salon) {
        // e.preventDefault();
        const options = {
            preserveScroll: true,
            onSuccess: (data: any) => {
                if (data.props.flash.success)
                  toast.success(flash.success)
                else
                  toast.error(flash.error)
              
                
                // toast.success($data.flash.success); // Message de succès
                // $form.defaults({...defaultForm}).clearErrors().reset();
                // onClose();
            },
            onError: (err) => toast.error(flash.error)
            // ; // Message d'erreur
            


        };
        $form.delete(`/salons/${salon.id}`, options)
    }
  </script>
  

<svelte:head>
  <title>Dashboard</title>
</svelte:head>

<AppLayout {breadcrumbs}>
  <div class="space-y-8">
    <Button onclick={() => openModal()}>Nouvel utilisateur</Button>
  
    <!-- Liste des utilisateurs -->
    <!-- {#each $page.props.salons as salon} -->
    {#each salons as salon}
    
      <div class="flex items-center justify-between p-4 border rounded-lg">
        <div>
          <p>{salon.name}</p>
          <p class="text-muted-foreground">{salon.description}</p>
        </div>
        <Button variant="outline" size="sm" onclick={() => openModal(salon)}>
          Éditer
        </Button>
        <Button variant="outline" size="sm" onclick={() => delsalon(salon)}>
          Suprimer
        </Button>
      </div>
    {/each}
  
    <ModelModal
      bind:open={isModalOpen}
      form={form}
      {flash}
      title={selectedItem ? 'Édition utilisateur' : 'Nouvel utilisateur'}
      description="Gestion des accès administrateur"
    >
      <div class="grid grid-cols-2 gap-4">
        <div class="space-y-2">
          <Label>Nom complet</Label>
          <Input name={$form.name} bind:value={$form.name} />
          {#if $form.errors.name}
            <p class="text-sm text-destructive">{$form.errors.name}</p>
          {/if}
        </div>
        <div class="space-y-2">
          <Label>description</Label>
          <Input name={$form.description} bind:value={$form.description} />
          <!-- {#if $form.errors.description}
            <p class="text-sm text-destructive">{$form.errors.description}</p>
          {/if} -->
          <InputError message={$form.errors.description} />
        </div>

        <div class="space-y-2">
          <Label>adresse</Label>
          <Input name={$form.adresse} bind:value={$form.adresse} />
          <!-- {#if $form.errors.description}
          <p class="text-sm text-destructive">{$form.errors.description}</p>
          {/if} -->
          <InputError  message={$form.errors.adresse} />
        </div>
      
        <div class="space-y-2">
          <Label>ville</Label>
          <Input name={$form.ville} bind:value={$form.ville} />
          <InputError  message={$form.errors.ville} />
        </div>
         <div class="space-y-2">
          <Label>code_postal</Label>
          <Input name={$form.code_postal} bind:value={$form.code_postal} />
          <InputError  message={$form.errors.code_postal} />
        </div>
        <div class="space-y-2">
          <Label>pays</Label>
          <Input name={$form.pays} bind:value={$form.pays} />
          <InputError  message={$form.errors.pays} />
        </div>
        
        <!-- si on veux des change en plus dans la creation -->
        <!--   
        {#if !selectedItem}
          <div class="space-y-2">
            <Label>Mot de passe</Label>
            <Input type="password" bind:value={form.data.password} />
            {#if form.errors.password}
              <p class="text-sm text-destructive">{form.errors.password}</p>
            {/if}
          </div>
        {/if} -->
      </div>
    </ModelModal>
  </div>
</AppLayout>