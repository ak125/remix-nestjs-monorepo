import { useEffect, useRef } from "react";
import * as THREE from "three";
import { GLTFLoader } from "three/examples/jsm/loaders/GLTFLoader";
import { OrbitControls } from "three/examples/jsm/controls/OrbitControls";

interface Car3DViewerProps {
  modelUrl: string;
  onLoad?: () => void;
}

export default function Car3DViewer({ modelUrl, onLoad }: Car3DViewerProps) {
  const containerRef = useRef<HTMLDivElement>(null);
  
  useEffect(() => {
    if (!containerRef.current) return;

    // Setup
    const scene = new THREE.Scene();
    const camera = new THREE.PerspectiveCamera(75, window.innerWidth / window.innerHeight, 0.1, 1000);
    const renderer = new THREE.WebGLRenderer({ antialias: true });
    
    // Lights
    const ambientLight = new THREE.AmbientLight(0xffffff, 0.5);
    const directionalLight = new THREE.DirectionalLight(0xffffff, 1);
    scene.add(ambientLight, directionalLight);

    // Controls
    const controls = new OrbitControls(camera, renderer.domElement);
    controls.enableDamping = true;

    // Load Model
    const loader = new GLTFLoader();
    loader.load(modelUrl, 
      (gltf) => {
        scene.add(gltf.scene);
        onLoad?.();
      },
      undefined,
      (error) => console.error('Error loading 3D model:', error)
    );

    // Animation
    function animate() {
      requestAnimationFrame(animate);
      controls.update();
      renderer.render(scene, camera);
    }

    camera.position.z = 5;
    animate();

    // Cleanup
    return () => {
      renderer.dispose();
      scene.clear();
    };
  }, [modelUrl]);

  return <div ref={containerRef} className="w-full h-[500px] rounded-lg" />;
}
