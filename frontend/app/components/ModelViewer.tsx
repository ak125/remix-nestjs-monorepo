import { useEffect, useState } from 'react';

interface ModelViewerProps {
  videoUrl?: string;
  arModelUrl?: string;
  onLoad?: () => void;
}

export default function ModelViewer({ videoUrl, arModelUrl, onLoad }: ModelViewerProps) {
  const [viewMode, setViewMode] = useState<'video' | 'ar'>('video');

  useEffect(() => {
    // Charger le script model-viewer pour l'AR
    const script = document.createElement('script');
    script.src = 'https://unpkg.com/@google/model-viewer/dist/model-viewer.min.js';
    script.type = 'module';
    document.head.appendChild(script);
    return () => { document.head.removeChild(script); };
  }, []);

  return (
    <div className="model-viewer-container">
      <div className="controls mb-4">
        <button 
          onClick={() => setViewMode('video')}
          className={`mr-2 px-4 py-2 rounded ${viewMode === 'video' ? 'bg-blue-500 text-white' : 'bg-gray-200'}`}
        >
          🎥 Vidéo
        </button>
        <button
          onClick={() => setViewMode('ar')}
          className={`px-4 py-2 rounded ${viewMode === 'ar' ? 'bg-blue-500 text-white' : 'bg-gray-200'}`}
        >
          📱 Réalité Augmentée
        </button>
      </div>

      <div className="viewer-content">
        {viewMode === 'video' && videoUrl ? (
          <iframe
            src={videoUrl}
            className="w-full aspect-video rounded-lg"
            allowFullScreen
          />
        ) : null}

        {viewMode === 'ar' && arModelUrl ? (
          // @ts-ignore
          <model-viewer
            src={arModelUrl}
            ar
            auto-rotate
            camera-controls
            className="w-full h-[500px]"
            onLoad={onLoad}
          />
        ) : null}
      </div>
    </div>
  );
}
