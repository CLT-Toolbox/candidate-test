import * as THREE from 'three';
import { FBXLoader } from 'three/addons/loaders/FBXLoader.js';
import { OrbitControls } from 'three/addons/controls/OrbitControls.js';
import { CSS2DRenderer, CSS2DObject } from 'three/addons/renderers/CSS2DRenderer.js';

// CONFIG
const CONFIG = {
    modelPath: 'model/wood/wood.fbx',
    backgroundColor: 0x000000,
};

class WoodsIllustration {
    constructor() {
        this.init();
        this.loadModel();
        this.animate();
    }

    // SCENE
    init() {
        this.scene = new THREE.Scene();
        this.scene.background = new THREE.Color(CONFIG.backgroundColor);

        // CAMERA
        this.camera = new THREE.PerspectiveCamera(28, window.innerWidth / window.innerHeight, 0.1, 1000);
        this.camera.position.set(6, 4, 6);

        // RENDERER
        this.container = document.getElementById('illustration-3d-container');
        this.renderer = new THREE.WebGLRenderer({ antialias: true });
        this.renderer.setSize(window.innerWidth, window.innerHeight);
        this.container.appendChild(this.renderer.domElement);

        // LABEL RENDERER
        this.labelRenderer = new CSS2DRenderer();
        this.labelRenderer.setSize(window.innerWidth, window.innerHeight);
        this.labelRenderer.domElement.style.position = 'absolute';
        this.labelRenderer.domElement.style.top = '0';
        this.labelRenderer.domElement.style.pointerEvents = 'none';
        this.container.appendChild(this.labelRenderer.domElement);

        // LIGHT
        this.scene.add(new THREE.AmbientLight(0xffffff, 0.8));
        const dirLight = new THREE.DirectionalLight(0xffffff, 1.2);
        dirLight.position.set(5, 10, 7.5);
        this.scene.add(dirLight);

        // AXIS
        this.axesHelper = new THREE.AxesHelper(5);
        this.scene.add(this.axesHelper);

        // CONTROLS
        this.controls = new OrbitControls(this.camera, this.renderer.domElement);
        this.controls.enableDamping = true;

        // RESIZE
        window.addEventListener('resize', () => this.onWindowResize());
    }

    // DIMENSION
    getDistance(start, end) {
        return start.distanceTo(end).toFixed(2).replace(/\.00$/, '') + 'm';
    }

    createLabel(text, position) {
        const div = document.createElement('div');
        div.className = 'label';
        div.textContent = text;
        div.style.color = '#fff';
        div.style.fontSize = '12px';
        div.style.background = 'rgba(0,0,0,0.5)';
        div.style.padding = '2px 6px';
        div.style.borderRadius = '4px';

        const label = new CSS2DObject(div);
        label.position.copy(position);
        return label;
    }

    // DIMENSION STYLE
    addDimension(start, end) {
        const group = new THREE.Group();
        const lineMat = new THREE.LineBasicMaterial({ color: 0xffffff });
        const geom = new THREE.BufferGeometry().setFromPoints([start, end]);
        group.add(new THREE.Line(geom, lineMat));

        const dotGeom = new THREE.SphereGeometry(0.03, 10, 10);
        const dotMat = new THREE.MeshBasicMaterial({ color: 0xffffff });

        const d1 = new THREE.Mesh(dotGeom, dotMat);
        d1.position.copy(start);
        const d2 = new THREE.Mesh(dotGeom, dotMat);
        d2.position.copy(end);

        group.add(d1, d2);

        const mid = new THREE.Vector3().lerpVectors(start, end, 0.5);
        mid.y += 0.05;

        group.add(this.createLabel(this.getDistance(start, end), mid));
        this.scene.add(group);
    }

    // TEXTURE FIX
    patchMaterial(material) {
        if (Array.isArray(material)) return material.forEach(m => this.patchMaterial(m));
        if (material.map) {
            material.map.wrapS = material.map.wrapT = THREE.RepeatWrapping;
            material.map.repeat.set(4, 4);
        }
    }

    // MODEL
    loadModel() {
        const loader = new FBXLoader();
        loader.load(CONFIG.modelPath, (fbx) => {
            let sourceMesh;
            fbx.traverse((c) => {
                if (c.isMesh && !sourceMesh) {
                    sourceMesh = c;
                    this.patchMaterial(c.material);
                }
            });

            const box = new THREE.Box3().setFromObject(sourceMesh);
            const size = box.getSize(new THREE.Vector3());
            this.ns = new THREE.Vector3(1 / size.x, 1 / size.y, 1 / size.z);
            this.sourceMesh = sourceMesh;

            this.buildIllustration();
        });
    }

    createBlock(w, h, d, x, y, z) {
        const b = this.sourceMesh.clone();
        this.patchMaterial(b.material);
        b.scale.set(w * this.ns.x, h * this.ns.y, d * this.ns.z);
        b.position.set(x + w / 2, y + h / 2, z + d / 2);
        this.scene.add(b);
    }

    // BLOCK
    buildIllustration() {
        this.createBlock(3.0, 0.2, 0.5, 0, 0, 0);
        this.createBlock(2.02, 0.2, 0.5, 0, 0, 0.5);
        this.createBlock(2.0, 0.2, 0.4, 0, 0.2, 0.4);

        // DIMENSIONS
        this.addDimension(new THREE.Vector3(0, 0.2, 0), new THREE.Vector3(3, 0.2, 0));
        this.addDimension(new THREE.Vector3(3, 0, 0), new THREE.Vector3(3, 0.2, 0));
        this.addDimension(new THREE.Vector3(3, 0, 0), new THREE.Vector3(3, 0, 0.5));
        this.addDimension(new THREE.Vector3(3, 0, 0.5), new THREE.Vector3(2.2, 0, 0.5));
        this.addDimension(new THREE.Vector3(2.0, 0, 0.5), new THREE.Vector3(2.0, 0, 1));
        this.addDimension(new THREE.Vector3(2.0, 0, 1), new THREE.Vector3(0, 0, 1));

        // DIMENSIONS ATAS
        this.addDimension(new THREE.Vector3(2.02, 0.2, 0.7), new THREE.Vector3(2.0, 0.2, 0.7));
        this.addDimension(new THREE.Vector3(2.0, 0.2, 0.8), new THREE.Vector3(2.0, 0.2, 0.4));
        this.addDimension(new THREE.Vector3(2.0, 0.4, 0.4), new THREE.Vector3(2.0, 0.2, 0.4));
        this.addDimension(new THREE.Vector3(2.0, 0.2, 0.8), new THREE.Vector3(0, 0.2, 0.8));

        this.controls.target.set(1, 0, 1);
        this.controls.update();
    }

    onWindowResize() {
        this.camera.aspect = window.innerWidth / window.innerHeight;
        this.camera.updateProjectionMatrix();
        this.renderer.setSize(window.innerWidth, window.innerHeight);
        this.labelRenderer.setSize(window.innerWidth, window.innerHeight);
    }

    // LOOP
    animate() {
        requestAnimationFrame(() => this.animate());
        this.controls.update();
        this.renderer.render(this.scene, this.camera);
        this.labelRenderer.render(this.scene, this.camera);
    }
}

new WoodsIllustration();