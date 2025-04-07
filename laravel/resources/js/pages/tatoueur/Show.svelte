<script>
    import { inertia, useForm, page } from '@inertiajs/svelte';

    // import { useForm } from '@inertiajs/svelte';

    // export let salon; // Réception des données du contrôleur

    // let updatedSalon = { ...salon }; // Copie pour permettre l'édition
    let errors = {}; // Gestion des erreurs
    let tatoueur = $derived($page.props.tatoueur|| null); 
    let disponibilites = $derived($page.props.disponibilites|| null); 
    let style = $derived(JSON.parse($page.props.style) || null); 
    
    
    const form = useForm({
        ...tatoueur,
        // 'salon': salon.id
        // name: salon.name,
        // description:salon.description,
        // adresse: salon.adresse,
        // ville: salon.ville,
        // code_postal: salon.code_postal,
        // pays: salon.pays,
    });

    // let { status, canResetPassword }: Props = $props();

    // const submit = () => {
    //     // e.preventDefault();
    //     $form.put(`/salons/${salon.id}`, {
    //         onFinish: () => $form.reset('password'),
    //     });
    // };
    const submitChanges = (e) => {
        e.preventDefault();
        $form.patch(route('tatoueurs.update', tatoueur), {
            preserveScroll: true,
        });
    };
    // const submitChanges = () => {
    //     form.put(`/salons/${salon.id}`, updatedSalon, {
    //         onSuccess: () => {
    //             alert('Salon modifié avec succès !');
    //         },
    //         onError: (err) => {
    //             errors = err; // Affiche les erreurs retournées par le backend
    //         },
    //     });
    // };
</script>

<div class="container mx-auto p-8">
    <h1 class="text-2xl font-bold text-black mb-6">Profil du Tatoueur</h1>
    <form on:submit|preventDefault={submitChanges} class="space-y-4">

    <div class="bg-white shadow-md rounded-lg p-6 space-y-4">
        <h2 class="text-xl font-semibold text-black">Détails Personnels</h2>
        <p class="text-black"><strong>Bio :</strong> {$form.bio}</p>
        <p class="text-black"><strong>Instagram :</strong>
            {#if $form.instagram}
                <a href={$form.instagram} target="_blank" class="text-blue-500 underline">
                    {$form.instagram}
                </a>
            {:else}
                <span class="text-black">Aucun compte Instagram</span>
            {/if}
        </p>

        <h2 class="text-xl font-semibold text-black">Styles</h2>
        {#if $form.style && $form.style.length > 0}
        <!-- <h2 class="text-xl font-semibold text-black">{$form.style}</h2> -->

            <ul class="list-disc text-black pl-5">
                <!-- {JSON.stringify(style, null, 2)}  -->
                {#each Object.entries(style.employees) as [key, value]}
                    <li><strong>{key} :</strong> {value}</li>
                {/each}
                <!-- {#each style as sty}
                {JSON.stringify(sty, null, 2)} 
                
                    <li>{sty}</li>
                    <li>{sty.firstName}</li>
                {/each} -->
            </ul>
        {:else}
            <p class="text-black">Aucun style défini.</p>
        {/if}

        <h2 class="text-xl font-semibold text-black">Disponibilités</h2>

        {#if disponibilites}
        <div class="space-y-4">
            
            {#each disponibilites.disponibilites as disponibilite}
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

        <h2 class="text-xl font-semibold text-black">Localisation Actuelle</h2>
        {#if $form.localisation_actuelle}
            <p class="text-black">ID du Salon : {$form.localisation_actuelle}</p>
        {:else}
            <p class="text-black">Aucune localisation définie.</p>
        {/if}
    </div>
    </form>

    <div class="mt-6">
        <a href="/tatoueurs" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
            Retour à la liste des Tatoueurs
        </a>
    </div>
</div>

