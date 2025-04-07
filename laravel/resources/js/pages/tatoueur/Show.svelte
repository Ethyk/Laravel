<script>
    import InputError from '@/components/InputError.svelte';
    import TextLink from '@/components/TextLink.svelte';
    import Input from '@/components/ui/input/input.svelte';
    import Label from '@/components/ui/label/label.svelte';
    import { inertia, useForm, page } from '@inertiajs/svelte';

    let { style, auth, disponibilites, tatoueur, salons } = $props()

    tatoueur.style = tatoueur.style ? JSON.parse(tatoueur.style) : tatoueur.style; // Convertit `style` en objet JSON
    tatoueur.disponibilites = tatoueur.disponibilites ? JSON.parse(tatoueur.disponibilites) : tatoueur.style; // Convertit `style` en objet JSON

    // Initialisation du formulaire avec les données transformées
    const form = useForm({
        ...tatoueur,
        _token: $page.props.csrf_token, // Ajoute le jeton CSRF
    });

    const submitChanges = (e) => {
        e.preventDefault();
        $form.style = JSON.stringify($form.style || {}); // Convertir style en chaîne JSON valide
        $form.disponibilites = JSON.stringify($form.disponibilites || {}); // Convertir disponibilites en chaîne JSON valide
        $form.patch(route('tatoueurs.update', $form), {
            preserveScroll: true,
            onSuccess: () => {
                 alert('profile modifié avec succès !');
            },
            onError: (err) => {
                // errors = err; // Affiche les erreurs retournées par le backend
                console.log(err);
            },
        });
        $form.style = JSON.parse($form.style || {}); // Convertir style en chaîne JSON valide
        $form.disponibilites = JSON.parse($form.disponibilites || {}); // Convertir disponibilites en chaîne JSON valide
       
    };
    const deleteTatoueur = () => {
        // e.preventDefault();
        $form.delete(route('tatoueurs.destroy', $form), {
            preserveScroll: true,
            onSuccess: () => {
                alert('profile supprime avec succès !');
                console.log($form);
            },
            onError: (err) => {
                // errors = err; // Affiche les erreurs retournées par le backend
            },
        });
    };
    
    // const addStyle = () => {
    //     $form.style = [...$form.style, { id: null, name: '', description: '' }];
    // };

    // Supprimer un style
    // const removeStyle = (index) => {
    //     console.log(index);
    //     $form.style.splice(index, 1);
    //     $form.style = [...$form.style]; // Reset array reference for reactivity
    // };

    const addStyle = () => {
        $form.style.push({
            id: Date.now() + Math.random().toString(36).substr(2, 9), // Generate a timestamp-based unique ID
            name: '',
            description: '',
            example_image_url: '',
        });
        $form.style = [...$form.style]; // Trigger reactivity
    };
    const removeStyle = (id) => {
        $form.style = $form.style.filter(style => style.id !== id); // Remove the style by its ID
    };
    // Helper function to find the index of a style by its id
    const findStyleIndex = (id) => {
        return $form.style.findIndex(style => style.id === id);
    };

    // let user = $derived($form.style);
    
    // let remaining = $derived(form.filter((t) => !t.name).length);
</script>

<div class="container mx-auto p-8">
    <h1 class="text-2xl font-bold text-black mb-6">Profil du Tatoueur</h1>
    <form onsubmit={submitChanges} class="space-y-4">

    <div class="bg-white shadow-md rounded-lg p-6 space-y-4">
        <h2 class="text-xl font-semibold text-black">Détails Personnels</h2>
        <div class="grid gap-2">
            <div class="flex items-center text-black justify-between">
                <Label for="bio"><strong>Bio :</strong> </Label>
                <!-- {#if canResetPassword} -->
                <!-- {/if} -->
            </div>
            <Input
                id="bio"
                type="text"
                required
                tabindex={2}
                autocomplete="current-password"
                bind:value={$form.bio}
                placeholder="bio"
            />
            <InputError message={$form.errors.bio} />
        </div>

        <div class="grid gap-2">
            <div class="flex items-center text-black justify-between">
                <Label for="instagram"><strong>instagram :</strong> </Label>
                <!-- {#if canResetPassword} -->
                <!-- {/if} -->
            </div>
            <Input
                id="instagram"
                type="text"
                required
                tabindex={2}
                autocomplete="current-password"
                bind:value={$form.instagram}
                placeholder="http://google.fr"
            />
            <InputError message={$form.errors.instagram} />
        </div>

        <h2 class="text-xl font-semibold text-black">Styles</h2>
        <!-- {JSON.stringify($form.style, null, 2)}  -->

        {#if $form.style}
                <!-- {JSON.stringify($form.style, null, 2)}  -->
                
        <!-- <h2 class="text-xl font-semibold text-black">{$form.style}</h2> -->
            <ul class="list-disc text-black pl-5">
                <!-- {JSON.stringify(style, null, 2)}  -->
                <!-- {#each Object.entries($form.style.employees) as [key, value]}
                    <li><strong>{key} :</strong> {value}</li>
                {/each} -->
                <!-- {$form.style.employees.length} -->
                {#if $form.style?.length > 0}
                    <!-- <ul class="text-black list-disc pl-5 text-black">
                       {#each $form.style as style, index}
                            <li>
                                <label for={`name-${index}`}>Nom du style :</label> 
                                <input id={`name-${index}`} type="text" bind:value={$form.style[index].name} />
                            </li>
                            <li>
                                <label for={`description-${index}`}>Nom du style :</label>
                                <input id={`description-${index}`} type="text" bind:value={$form.style[index].description} />
                            </li>
                            <li>
                                <label for={`example-${index}`}>URL de l'image :</label>
                                <input id={`example-${index}`} type="text" bind:value={$form.style[index].example_image_url} />
                            </li>
                            <li>
                                <button type="button" onclick={() => removeStyle(index)} class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">
                                    Supprimer ce style
                                </button>
                            </li>
                        {/each} 
                        <button type="button" onclick={addStyle} class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                            Ajouter un nouveau style
                        </button>
                    </ul> -->
                    <ul class="text-black list-disc pl-5 text-black">
                        {#each $form.style as style (style.id)}
                             <li>
                                 <label for={`name-${style.id}`}>Nom du style :</label> 
                                 <input id={`name-${style.id}`} type="text" bind:value={$form.style[findStyleIndex(style.id)].name} />
                             </li>
                             <li>
                                 <label for={`description-${style.id}`}>Nom du style :</label>
                                 <input id={`description-${style.id}`} type="text" bind:value={$form.style[findStyleIndex(style.id)].description} />
                             </li>
                             <li>
                                 <label for={`example-${style.id}`}>URL de l'image :</label>
                                 <!-- Dynamically locate the style by id -->
                                 <input id={`example-${style.id}`} type="text" bind:value={$form.style[findStyleIndex(style.id)].example_image_url}  />
                             </li>
                             <li>
                                 <button type="button" onclick={() => removeStyle(style.id)} class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">
                                     Supprimer ce style
                                 </button>
                             </li>
                         {/each} 
                         <button type="button" onclick={addStyle} class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                             Ajouter un nouveau style
                         </button>
                     </ul>
                {:else}
                    <p class="text-black">Aucune plage horaire pour ce jour.</p>
                    <button type="button" onclick={addStyle} class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                        Ajouter un nouveau style
                    </button>
                {/if}
                <!-- {JSON.stringify($form.style.employees, null, 2)}
                {JSON.stringify($form.style.employees.lastName, null, 2)}  -->
                {#each $form.style.employees as employee}
                
                    <li>{employee.firstName}</li>
                    <li>{employee.lastName}</li>
                {/each}
            </ul>
            
        {:else}
            <p class="text-black">Aucun style défini.</p>
        {/if}

        <h2 class="text-xl font-semibold text-black">Disponibilités</h2>

        {#if $form.disponibilites}
        <div class="space-y-4">
            
            <!-- {#each JSON.parse($form.disponibilites).disponibilites as disponibilite} -->
            {#each $form.disponibilites.disponibilites as disponibilite}
                <div>
                   
                    <h3 class="text-lg font-medium capitalize text-black">{disponibilite.jour}</h3>
                    {#if disponibilite.plages_horaires?.length > 0}
                        <ul class="text-black list-disc pl-5 text-black">
                            {#each disponibilite.plages_horaires as plage}
                                <li>De {plage.debut} à {plage.fin}</li>
                            {/each}
                        </ul>
                    {:else}
                        <p class="text-black">Aucune plage horaire pour ce jour.</p>
                    {/if}
                </div>
            {/each}
        </div>
        {:else}
            <p class="text-black">Aucune disponibilité définie.</p>
        {/if}





        <div  >
            <label for="salon" class="block text-black text-sm font-medium"><strong>Salon :</strong> </label>
       
            <select
                id="salon"
                bind:value={$form.localisation_actuelle}
                class="w-full border text-black rounded px-4 py-2"
            >
                <option value="" disabled selected>Choisissez un salon</option>
                {#each salons as salon}
                    <option class="text-gray-500" value={salon.id}   >{salon.name}</option>
                {/each}
            </select>
            {#if $form.errors.localisation_actuelle}
                <!-- <p class="text-red-500 text-sm mt-1">{$form.errors.localisation_actuelle}</p> -->
                <InputError message={$form.errors.localisation_actuelle} />

            {/if}
            
        </div>

        <div class="grid gap-2">
            
            <Input
                id="localisation_actuelle"
                type="text"
                required
                tabindex={2}
                autocomplete="current-password"
                bind:value={$form.localisation_actuelle}
                placeholder="http://google.fr"
            />
            <InputError message={$form.errors.localisation_actuelle} />
        </div>
        <h2 class="text-xl font-semibold text-black">Localisation Actuelle</h2>
        {#if $form.localisation_actuelle}
            <p class="text-black">ID du Salon : {$form.localisation_actuelle}</p>
        {:else}
            <p class="text-black">Aucune localisation définie.</p>
        {/if}
    </div>
    <button
    type="submit"
    class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600"
>
    Modifier le profile
</button>
    </form>
   
    <div class="mt-6">
        
        <button
        onclick={() => deleteTatoueur()}
        class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600"
    >
        Supprimer le profil Tatoueur
    </button>
        <a href="/tatoueurs" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
            Retour à la liste des Tatoueurs
        </a>
    </div>
</div>

