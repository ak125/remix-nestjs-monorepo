import PieceModal from "~/composants/PieceModal";

export default function IndexPage() {
  return (
    <div className="flex flex-col items-center space-y-4 p-6">
      <h1 className="text-2xl font-bold">Catalogue Pièces</h1>
      <PieceModal />
    </div>
  );
}
