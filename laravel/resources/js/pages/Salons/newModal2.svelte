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
    import { Flashlight } from 'lucide-svelte';

    let {
      children,
    // open = $bindable(false), // Spécifie la valeur par défaut ET rend la prop bindable
    open = $bindable(false), // Spécifie la valeur par défaut ET rend la prop bindable
    form,
    title = '',
    description = '',
    flash,
    last
  } = $props();
  
    // $: isEdit = !!form?.data?.id;
    // $derived(isEdit = !!form?.data?.id);
    let isEdit = $derived(!!$form?.data()?.id);

    // function submit(e){
    //   e.preventDefault()
    //   console.log($form.data());
    //   $form.submit(isEdit ? 'put' : 'post', {
    //         onSuccess: () => (open = false),
    //       });
    // }

     const focusOnErrorField = () =>
    Object.keys($form.errors).some(key =>
        (document.querySelector(`[name="${$form[key]}"]`)?.focus(), !!document.querySelector(`[name="${$form[key]}"]`))
    );

    function saveData(e) {
      e.preventDefault();
      const options = {
        preserveScroll: true,
        onSuccess: (data) => {
          // onClose();
          if (isEdit) {
            // Toast pour update
            toast.success(flash.success);
          } else {
            // console.log("dataaaaaaa",data.props);
            // console.log("last: ",last);
            // console.log("isEditi",isEdit);
            // console.log("data : ",$form.data())
            
            // Toast pour post avec action personnalisée
            toast(flash.success, {
              action: {
                label: 'Annuler',
                onClick: () => $form.delete(`/salons/${last.id}`, {onSuccess: () => { toast.success(flash.success);} }),
              },
            });
          }
          $form.clearErrors().reset();
          open = false;
          },
        onError: () => {
          console.log(flash)
          toast.error(flash.error)
          focusOnErrorField();
        }
        
        };
        isEdit
            ? $form.patch(`/salons/${$form?.data()?.id}`, options)
            : $form.post(`/salons/`, options);
    }
    function quit(){
      // if (open)
      $form.clearErrors();
      open=false;
    }

  </script>
  
  <Dialog.Root {open} onOpenChange={quit}>
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
          <Button type="button" variant="outline" onclick={() => quit()}>
            Annuler
          </Button>
          <Button type="submit" disabled={form.processing}>
            {form.processing ? 'Enregistrement...' : 'Enregistrer'}
          </Button>
        </Dialog.Footer>
      </form>
    </Dialog.Content>
  </Dialog.Root>