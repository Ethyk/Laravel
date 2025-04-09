<script>
    // import { Dialog } from '@/components/ui/dialog';
    import { Label } from '@/components/ui/label';
    // import { Button } from '@/components/ui/button';
    import { toast } from 'svelte-sonner';
    
    import {
        Button,
        buttonVariants
    } from "@/components/ui/button/index.js";
  
    import * as Dialog from "@/components/ui/dialog/index";

    let {
      children,
    // open = $bindable(false), // Spécifie la valeur par défaut ET rend la prop bindable
    open = $bindable(false), // Spécifie la valeur par défaut ET rend la prop bindable
    form,
    title = '',
    description = ''
  } = $props();
  
    // $: isEdit = !!form?.data?.id;
    // $derived(isEdit = !!form?.data?.id);
    let isEdit = $derived(!!$form?.data()?.id);

    // function submit(e){
    //   e.preventDefault()
    //   console.log($form.data());
    //   $form.submit(isEdit ? 'put' : 'post', {
    //         onSuccess: () => (open = false),
    //         // onError: () => toast.error('Une erreur est survenue')
    //       });
    // }
    // console.log("data : ",$form.data().id)
    function saveData(e) {
        e.preventDefault();
        const options = {
            preserveScroll: true,
            onSuccess: () => {
                $form.clearErrors().reset();
                // onClose();
                open = false;
            },
            onError: (err) => console.log(err)
        };
        isEdit
            ? $form.patch(`/salons/${$form?.data()?.id}`, options)
            : $form.post(`/salons/`, options);
    }

  </script>
  
  <Dialog.Root {open} onOpenChange={() => (open = false)}>
    <!-- <Dialog.Overlay class="bg-black/50 backdrop-blur-sm" /> -->
    <Dialog.Content class="max-w-2xl">
      <Dialog.Header>
        <Dialog.Title>{title}</Dialog.Title>
        <Dialog.Description>{description}</Dialog.Description>
        <Dialog.Close  />
      </Dialog.Header>
<!--   
      <form
        class="space-y-6"
        onsubmit|preventDefault={() => {
          form.submit(isEdit ? 'put' : 'post', {
            onSuccess: () => (open = false),
            // onError: () => toast.error('Une erreur est survenue')
          });
        }}
      > -->

      <form
        class="space-y-6"
        onsubmit={saveData}
      >

        <div class="grid gap-4 py-4">
          <!-- Slot pour les champs du formulaire -->
          <!-- <slot {form} /> -->
          {@render children({ form })}
        </div>
  
        <Dialog.Footer>
          <Button type="button" variant="outline" onclick={() => (open = false)}>
            Annuler
          </Button>
          <Button type="submit" disabled={form.processing}>
            {form.processing ? 'Enregistrement...' : 'Enregistrer'}
          </Button>
        </Dialog.Footer>
      </form>
    </Dialog.Content>
  </Dialog.Root>