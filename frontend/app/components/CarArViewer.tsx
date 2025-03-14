import { useEffect, useRef } from 'react';

interface CarArViewerProps {
  modelUrl: string;
  name: string;
  onLoad?: () => void;
}

export default function CarArViewer({ modelUrl, name, onLoad }: CarArViewerProps) {
  const viewerRef = useRef<any>(null);

  useEffect(() => {
    const script = document.createElement('script');
    script.src = 'https://unpkg.com/@google/model-viewer/dist/model-viewer.min.js';
    script.type = 'module';
    document.head.appendChild(script);

    return () => {
      document.head.removeChild(script);
    };
  }, []);

  return (
    <div className="ar-viewer-container">
      {/* @ts-ignore */}
      <model-viewer
        ref={viewerRef}
        src={modelUrl}
        alt={`Modèle 3D de ${name}`}
        ar
        ar-modes="webxr scene-viewer quick-look"
        camera-controls
        environment-image="neutral"
        auto-rotate
        className="w-full h-[500px]"
        onLoad={onLoad}
      >
        <button slot="ar-button" className="ar-button">
          👀 Voir en AR
        </button>
      </model-viewer>
    </div>
  );
}
