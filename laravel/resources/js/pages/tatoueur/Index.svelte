<!-- src/Pages/Tatoueur/Index.svelte -->
<script lang="ts">
	import GenericModal from '@/components/GenericModal.svelte';
	import { useForm, page } from '@inertiajs/svelte';
	import { Input } from '@/components/ui/input/index.js';
	import { Textarea } from '@/components/ui/textarea/index.js';
	import { Label } from '@/components/ui/label/index.js';
	import { Button } from '@/components/ui/button/index.js';
	import * as Select from '@/components/ui/select/index';
	import InputError from '@/components/InputError.svelte';
	import AppLayout from '@/layouts/AppLayout.svelte';
	import { type BreadcrumbItem } from '@/types';
	import { toast } from 'svelte-sonner';
	import { Trash2, Edit, PlusCircle } from 'lucide-svelte';

	// Props (utilisation de $props pour réactivité)
	let {
		tatoueur = null,
		salonsDisponibles = [],
		csrf_token = '',
		flash = null
	} = $props();

	const breadcrumbs: BreadcrumbItem[] = [
		{ title: 'Dashboard', href: '/dashboard' },
		{ title: 'Mon Profil Tatoueur', href: '/tatoueurs' }
	];

	// États locaux
	let isModalOpen = $state(false);
	let isEdit = $derived(!!tatoueur?.id);

	// // --- Fonctions utilitaires JSON ---
	// // Nécessaire pour la conversion objet <-> chaîne pour la textarea 'disponibilites'
	// function safeStringify(obj: any): string {
	// 	try {
    //         return JSON.stringify(obj ?? (defaultValue === '{}' ? {} : []), null, 2);

	// 		return JSON.stringify(obj ?? {}, null, 2); // Pretty print, gère null/undefined
	// 	} catch {
	// 		return '{}'; // Fallback
	// 	}
	// }

    // --- Fonction utilitaire JSON (juste pour l'initialisation et l'affichage) ---
	function safeStringify(obj: any, defaultValue = '{}'): string {
		try {
			// Stringify, utilise une valeur par défaut si l'entrée est null/undefined
			return JSON.stringify(obj ?? (defaultValue === '{}' ? {} : []), null, 2);
		} catch {
			return defaultValue; // Retourne '{}' ou '[]' en cas d'erreur
		}
	}

	function safeParse(str: string): any {
		try {
			if (!str?.trim()) return {}; // Objet vide si chaîne vide
			return JSON.parse(str);
		} catch (e) {
			// console.warn("Invalid JSON input:", e); // Optionnel: log l'erreur
			return { json_error: 'Format JSON invalide' }; // Marqueur d'erreur
		}
	}

	// --- Formulaire Inertia ---
	// Initialise avec les types corrects (tableaux/objets grâce à $casts)
	const form = useForm({
		id: tatoueur?.id ?? null,
		bio: tatoueur?.bio ?? '',
		style: tatoueur?.style ?? [], // Devrait être un tableau
		disponibilites: tatoueur?.disponibilites ?? {}, // Devrait être un objet/tableau
		localisation_actuelle: tatoueur?.localisation_actuelle ?? null, // ID (string) ou null
		instagram: tatoueur?.instagram ?? '',
		_token: csrf_token
	});

	// --- États locaux liés aux UI (Input/Textarea) ---
	// Nécessaires car bind:value ne peut pas lier directement des tableaux/objets
	// let styleString = $state($form.style?.join(', ') ?? '');
	// let disponibilitesJsonString = $state(safeStringify($form.disponibilites));

	// --- Synchronisation : Formulaire -> États locaux UI ---
	// $effect(() => {
	// 	// Met à jour la chaîne pour l'input 'style' si le tableau $form.style change
	// 	const currentArrayString = $form.style?.join(', ') ?? '';
	// 	if (styleString !== currentArrayString) {
	// 		styleString = currentArrayString;
	// 	}
	// });

	// $effect(() => {
	// 	// Met à jour la chaîne JSON pour la textarea 'disponibilites' si l'objet $form.disponibilites change
	// 	const currentObjectString = safeStringify($form.disponibilites);
	// 	if (disponibilitesJsonString !== currentObjectString) {
	// 		disponibilitesJsonString = currentObjectString;
	// 		// Efface une potentielle erreur de parsing si l'objet redevient valide
	// 		if ($form.errors.disponibilites === 'Format JSON invalide.' && !safeParse(currentObjectString)?.json_error) {
	// 			$form.clearErrors('disponibilites');
	// 		}
	// 	}
	// });

	// --- Calcul dérivé pour l'affichage dans le Select Trigger ---
	// (Garde cette approche car elle est robuste pour l'affichage)
	const selectedSalonLabel = $derived(() => {
		const selectedId = $form.localisation_actuelle;
		if (selectedId === null) {
			return 'Aucun';
		}
		if (selectedId) {
			const foundSalon = (salonsDisponibles ?? []).find((salon) => salon.id === selectedId);
			if (foundSalon) {
				return `${foundSalon.name} (${foundSalon.ville})`;
			}
		}
		return 'Sélectionnez un salon'; // Placeholder/fallback
	});

    const triggerContent = $derived(
        salonsDisponibles.find((f) => f.id === $form.localisation_actuelle)?.name ?? "Select a fruit"
    );

	// --- Handlers ---
	const openModal = () => {
		$form.reset(); // Réinitialise $form aux valeurs initiales ou props
		$form.clearErrors();

		// Réaffectation explicite après reset pour garantir les types et valeurs par défaut
		if (tatoueur) {
			$form.id = tatoueur.id;
			$form.bio = tatoueur.bio ?? '';
			$form.style = tatoueur.style ?? [];
			$form.disponibilites = tatoueur.disponibilites ?? {};
			$form.localisation_actuelle = tatoueur.localisation_actuelle ?? null; // !! CORRIGÉ: null !!
			$form.instagram = tatoueur.instagram ?? '';
		} else {
			// Valeurs par défaut pour la création
			$form.id = null;
			$form.bio = '';
			$form.style = [];
			$form.disponibilites = {};
			$form.localisation_actuelle = null; // !! CORRIGÉ: null !!
			$form.instagram = '';
		}
		// Les $effect synchroniseront styleString et disponibilitesJsonString
		$form._token = csrf_token; // Assure que le token est frais
		isModalOpen = true;
	};

	// Handler UI -> Formulaire pour 'style'
	function handleStyleInput(event: Event) {
		const input = event.target as HTMLInputElement;
		styleString = input.value; // Met à jour l'état lié à l'input
		// Met à jour le tableau dans $form
		$form.style = input.value
			.split(',')
			.map((s) => s.trim())
			.filter((s) => s.length > 0);
	}

	// Handler UI -> Formulaire pour 'disponibilites'
	function handleDisponibilitesInput(event: Event) {
		const textarea = event.target as HTMLTextAreaElement;
		disponibilitesJsonString = textarea.value; // Met à jour l'état lié à la textarea
		const parsedObject = safeParse(textarea.value);
		$form.disponibilites = parsedObject; // Met à jour l'objet dans $form

		// Efface l'erreur si JSON redevient valide
		if ($form.errors.disponibilites === 'Format JSON invalide.' && !parsedObject?.json_error) {
			$form.clearErrors('disponibilites');
		}
	}

	const handleSubmit = () => {
		// Vérification de l'erreur de parsing JSON avant soumission
		if ($form.disponibilites?.json_error) {
			$form.setError('disponibilites', 'Format JSON invalide.');
			toast.error('Veuillez corriger le format JSON des disponibilités.');
			focusOnErrorField();
			return;
		}

		const options = {
			preserveScroll: true,
			onSuccess: (pageData: any) => {
				const currentFlash = pageData.props.flash;
				toast.success(currentFlash?.success || `Profil ${isEdit ? 'mis à jour' : 'créé'} avec succès.`);
				isModalOpen = false; // Ferme le modal après succès
			},
			onError: (errors: any) => {
				const errorMsg = $page.props.flash?.error || 'Une erreur est survenue. Veuillez vérifier les champs.';
				toast.error(errorMsg);
				focusOnErrorField(); // Met le focus sur le premier champ en erreur
			}
		};

		// Soumission : Inertia enverra les données de $form (avec tableaux/objets)
		// Laravel les validera comme 'json' et le modèle les enregistrera correctement via $casts
		if (isEdit) {
			$form.patch(`/tatoueurs/${$form.id}`, options);
		} else {
			$form.post('/tatoueurs', options);
		}
	};

	const handleCancel = () => {
		$form.clearErrors();
		isModalOpen = false;
	};

	const focusOnErrorField = () => {
		const errorKeys = Object.keys($form.errors);
		if (errorKeys.length > 0) {
			const field = document.querySelector<HTMLElement>(`[name="${errorKeys[0]}"]`);
			field?.focus();
		}
	};

	const handleDelete = () => {
		if (!tatoueur?.id) return;
		if (confirm('Êtes-vous sûr de vouloir supprimer définitivement votre profil tatoueur ?')) {
			const options = {
				preserveScroll: true,
				onSuccess: (pageData: any) => {
					toast.success(pageData.props.flash?.success || 'Profil supprimé.');
				},
				onError: () => {
					toast.error(page().props.flash?.error || 'Erreur lors de la suppression.');
				}
			};
			// Utiliser un form temporaire pour le delete permet de gérer l'état processing
			useForm({}).delete(`/tatoueurs/${tatoueur.id}`, options);
		}
	};

    function displayDataAsJson(data: any, defaultValue = 'Non défini'): string {
		if (!data || (typeof data === 'object' && Object.keys(data).length === 0)) {
			return defaultValue;
		}
		try {
			// Tente de stringifier pour affichage formaté
			const str = JSON.stringify(data, null, 2);
			// Vérifie si c'est un objet/tableau vide après stringify
			if (str === '{}' || str === '[]') {
				return defaultValue;
			}
			return str;
		} catch {
			// Si erreur (ne devrait pas arriver avec des données PHP castées), affiche brut
			return String(data);
		}
	}
	// // --- Fonctions d'affichage ---
	// function displayStyles(stylesArray: string[] | null | undefined): string {
	// 	return stylesArray?.length ? stylesArray.join(', ') : 'Non défini';
	// }

	function displayDisponibilites(dispo: any): string {
		const str = safeStringify(dispo);
		return str === '{}' || str === '[]' ? 'Non défini' : str;
	}
</script>

<svelte:head>
	<title>Mon Profil Tatoueur</title>
</svelte:head>

<AppLayout {breadcrumbs}>
	<div class="space-y-8">
		{#if tatoueur}
			<!-- Affichage du Profil Existant -->
			<div class="p-6 border rounded-lg shadow-sm space-y-4">
				<div class="flex justify-between items-center flex-wrap gap-2">
					<h2 class="text-2xl font-semibold">Votre Profil Tatoueur</h2>
					<div class="flex gap-2 flex-shrink-0">
						<Button variant="outline" size="sm" onclick={openModal}>
							<Edit class="w-4 h-4 mr-2" /> Éditer
						</Button>
						<Button variant="destructive" size="sm" onclick={handleDelete}>
							<Trash2 class="w-4 h-4 mr-2" /> Supprimer
						</Button>
					</div>
				</div>

				<div>
					<h3 class="font-medium mb-1">Bio</h3>
					<p class="text-muted-foreground whitespace-pre-wrap">{tatoueur.bio || 'Non définie'}</p>
				</div>

				<div>
					<h3 class="font-medium mb-1">Styles (JSON brut)</h3>
					<pre class="text-sm bg-muted p-3 rounded overflow-x-auto"><code>{displayDataAsJson(tatoueur.style)}</code></pre>
				</div>

				<div>
					<h3 class="font-medium mb-1">Salon Actuel</h3>
					<p class="text-muted-foreground">
						{tatoueur.salonActuel?.name
							? `${tatoueur.salonActuel.name} (${tatoueur.salonActuel.ville})`
							: 'Non défini'}
					</p>
				</div>

				<div>
					<h3 class="font-medium mb-1">Instagram</h3>
					{#if tatoueur.instagram}
						<a
							href={tatoueur.instagram}
							target="_blank"
							rel="noopener noreferrer"
							class="text-blue-600 hover:underline break-all"
							>{tatoueur.instagram}</a
						>
					{:else}
						<p class="text-muted-foreground">Non défini</p>
					{/if}
				</div>

				<div>
					<h3 class="font-medium mb-1">Disponibilités</h3>
					<pre class="text-sm bg-muted p-3 rounded overflow-x-auto">
						<code>{displayDataAsJson(tatoueur.disponibilites)}</code>
					</pre>
				</div>
			</div>
		{:else}
			<!-- Invitation à Créer un Profil -->
			<div class="text-center p-6 border rounded-lg shadow-sm">
				<p class="text-muted-foreground mb-4">Vous n'avez pas encore de profil tatoueur.</p>
				<Button onclick={openModal}> <PlusCircle class="w-4 h-4 mr-2" /> Créer mon profil </Button>
                <div class="bg-black/95 p-2  text-left  rounded  text-white text-xs font-mono whitespace-pre-wrap">
                    {JSON.stringify($form.data(), null, 2)}
                  </div>
			</div>
		{/if}

		<!-- Le Modal Générique -->
		<GenericModal
			bind:open={isModalOpen}
			title={isEdit ? 'Modifier mon Profil' : 'Créer mon Profil'}
			description="Renseignez les informations de votre profil tatoueur."
			isProcessing={$form.processing}
			onSubmit={handleSubmit}
			onCancel={handleCancel}
			submitLabel={isEdit ? 'Mettre à jour' : 'Créer'}
		>
			<!-- Contenu du formulaire -->
			<div class="grid grid-cols-1 gap-4">
				<!-- Bio -->
				<div class="space-y-2">
					<Label for="bio">Bio</Label>
					<Textarea
						id="bio"
						name="bio"
						bind:value={$form.bio}
						placeholder="Décrivez votre parcours, votre style..."
						aria-invalid={$form.errors.bio ? 'true' : undefined}
					/>
					<InputError message={$form.errors.bio} />
				</div>

				<!-- Style -->
				<div class="space-y-2">
					<Label for="style">Styles (séparés par des virgules)</Label>
					<Input
						id="style"
						name="style"
						placeholder="ex: Old School, Japonais, Dotwork"
						bind:value={$form.style}
						aria-invalid={$form.errors.style ? 'true' : undefined}
					/>
					<InputError message={$form.errors.style} />
				</div>

				<!-- Localisation Actuelle -->
				<div class="space-y-2">
					<Label for="localisation_actuelle">Salon Actuel (Optionnel)</Label>
					<Select.Root
						name="localisation_actuelle"
						bind:value={$form.localisation_actuelle}
						preventScroll={true}
                        type="single"
					>
						<Select.Trigger
							id="localisation_actuelle"
							class="w-full"
							aria-invalid={$form.errors.localisation_actuelle ? 'true' : undefined}
						>
							{triggerContent}
						</Select.Trigger>

						<Select.Content>
							<Select.Item value={null} label="Aucun">Aucun</Select.Item>
							{#each salonsDisponibles as salon (salon.id)}
								<Select.Item value={salon.id} label={`${salon.name} (${salon.ville})`}>
									{salon.name} ({salon.ville})
								</Select.Item>
							{/each}
						</Select.Content>
					</Select.Root>
					<InputError message={$form.errors.localisation_actuelle} />
				</div>

				<!-- Instagram -->
				<div class="space-y-2">
					<Label for="instagram">Lien Instagram (URL complète)</Label>
					<Input
						id="instagram"
						name="instagram"
						type="url"
						bind:value={$form.instagram}
						placeholder="https://instagram.com/votreprofil"
						aria-invalid={$form.errors.instagram ? 'true' : undefined}
					/>
					<InputError message={$form.errors.instagram} />
				</div>

				<!-- Disponibilités -->
				<div class="space-y-2">
					<Label for="disponibilites">Disponibilités (JSON)</Label>
					<Textarea
						id="disponibilites"
						name="disponibilites"
						rows={5}
						placeholder={'{\n  "lundi": "fermé",\n  "mardi": "10h-18h",\n  "mercredi": "10h-18h"\n}'}
                        bind:value={$form.disponibilites} 

						aria-invalid={$form.errors.disponibilites ? 'true' : undefined}
					/>
					<InputError message={$form.errors.disponibilites} />
					{#if $form.disponibilites?.json_error && !$form.errors.disponibilites}
						<!-- Afficher l'erreur de parsing seulement si pas d'erreur backend -->
						<p class="text-sm text-destructive">Format JSON invalide.</p>
					{/if}
				</div>
			</div>
		</GenericModal>
	</div>
</AppLayout>