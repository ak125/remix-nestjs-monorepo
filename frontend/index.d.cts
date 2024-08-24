declare module '@fafa/frontend' {
	// <= Nom du module, à adapter selon le nom de votre projet
	export function getPublicDir(): string;
	export function getServerBuild(): Promise<any>;
	export function startDevServer(app: any): Promise<void>;
}