import * as THREE from 'three';
import { FBXLoader } from 'three/addons/loaders/FBXLoader.js';
import { OrbitControls } from 'three/addons/controls/OrbitControls.js';

const VIEWPORT = {
    width: 793,
    height: 628,
};

const container = document.getElementById('illustration-3d-container');
const dimensionLayer = document.getElementById('dimension-layer');
const statusLayer = document.getElementById('status-layer');
container.dataset.state = 'loading';

const scene = new THREE.Scene();
scene.background = new THREE.Color(0x000000);

const camera = new THREE.PerspectiveCamera(35, VIEWPORT.width / VIEWPORT.height, 0.1, 100);
camera.position.set(5.25, 3.65, 4.95);

const renderer = new THREE.WebGLRenderer({
    antialias: true,
    alpha: false,
});
renderer.outputColorSpace = THREE.SRGBColorSpace;
renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
renderer.setSize(VIEWPORT.width, VIEWPORT.height);
container.prepend(renderer.domElement);

const controls = new OrbitControls(camera, renderer.domElement);
controls.enableDamping = true;
controls.enablePan = false;
controls.minDistance = 4;
controls.maxDistance = 14;
controls.target.set(0.9, 0.4, -0.75);

const illustrationGroup = new THREE.Group();
scene.add(illustrationGroup);

const axesHelper = new THREE.AxesHelper(6.5);
scene.add(axesHelper);

scene.add(new THREE.HemisphereLight(0xffffff, 0x221707, 1.35));

const fillLight = new THREE.DirectionalLight(0xfff7df, 1.4);
fillLight.position.set(4.2, 6.5, 3.2);
scene.add(fillLight);

const rimLight = new THREE.DirectionalLight(0xc9b385, 0.7);
rimLight.position.set(-5.5, 2.5, -4.5);
scene.add(rimLight);

const ambientLight = new THREE.AmbientLight(0x705c3a, 0.25);
scene.add(ambientLight);

const labelEntries = [];
const dimensionGroup = new THREE.Group();
scene.add(dimensionGroup);

const worldExtents = {
    leftBase: {
        minX: -0.25,
        maxX: 0.25,
        minY: 0.0,
        maxY: 0.2,
        minZ: -2.0,
        maxZ: 0.0,
    },
    rightBase: {
        minX: 0.0,
        maxX: 3.0,
        minY: 0.0,
        maxY: 0.2,
        minZ: -0.25,
        maxZ: 0.25,
    },
    topBeam: {
        minX: -0.23,
        maxX: 0.17,
        minY: 0.2,
        maxY: 0.4,
        minZ: -2.0,
        maxZ: 0.0,
    },
};

const loaderManager = new THREE.LoadingManager();
const woodResourceUrl = new URL('../../model/wood/wood.fbm/', import.meta.url).href;
const woodFbxUrl = new URL('../../model/wood/wood.fbx', import.meta.url).href;

const textureAliases = new Map([
    ['colormap.png', new URL('../../model/wood/wood.fbm/Colormap.png', import.meta.url).href],
    ['color_a02.jpg', new URL('../../model/wood/wood.fbm/Color_A02.jpg', import.meta.url).href],
    ['normalmap.png', new URL('../../model/wood/wood.fbm/NormalMap.png', import.meta.url).href],
    ['normal_map.png', new URL('../../model/wood/wood.fbm/normal_map.png', import.meta.url).href],
]);

loaderManager.setURLModifier((url) => {
    const fileName = decodeURIComponent(url).split(/[\\/]/).pop()?.toLowerCase();

    if (fileName && textureAliases.has(fileName)) {
        return textureAliases.get(fileName);
    }

    return url;
});

const textureLoader = new THREE.TextureLoader(loaderManager);
const colorMap = textureLoader.load(textureAliases.get('colormap.png'));
colorMap.colorSpace = THREE.SRGBColorSpace;
const normalMap = textureLoader.load(textureAliases.get('normalmap.png'));

const modelLoader = new FBXLoader(loaderManager);
modelLoader.setResourcePath(woodResourceUrl);
modelLoader.load(woodFbxUrl, handleModelReady, undefined, handleModelError);

controls.update();
animate();

function handleModelReady(modelRoot) {
    const preparedModel = prepareModel(modelRoot);

    createBeam({
        name: 'leftBase',
        preparedModel,
        length: 2.0,
        width: 0.5,
        height: 0.2,
        position: new THREE.Vector3(0, 0, -1),
        rotationY: Math.PI / 2,
    });

    createBeam({
        name: 'rightBase',
        preparedModel,
        length: 3.0,
        width: 0.5,
        height: 0.2,
        position: new THREE.Vector3(1.5, 0, 0),
        rotationY: 0,
    });

    createBeam({
        name: 'topBeam',
        preparedModel,
        length: 2.0,
        width: 0.4,
        height: 0.2,
        position: new THREE.Vector3(-0.03, 0.2, -1),
        rotationY: Math.PI / 2,
    });

    buildDimensions();
    container.dataset.state = 'ready';
    statusLayer.textContent = '';
    statusLayer.style.display = 'none';
}

function handleModelError(error) {
    console.error(error);
    container.dataset.state = 'error';
    statusLayer.style.display = 'flex';
    statusLayer.textContent = 'Failed to load the FBX wood model.';
}

function prepareModel(modelRoot) {
    const source = modelRoot.clone(true);
    const bestRotation = findBestOrthogonalRotation(source);

    source.rotation.set(bestRotation.x, bestRotation.y, bestRotation.z);
    source.updateMatrixWorld(true);

    source.traverse((child) => {
        if (!child.isMesh) {
            return;
        }

        child.castShadow = false;
        child.receiveShadow = false;

        const hasMaterialArray = Array.isArray(child.material);
        const materials = hasMaterialArray ? child.material : [child.material];
        const tunedMaterials = cloneMaterials(materials).map((material) => tuneMaterial(material));
        child.material = hasMaterialArray ? tunedMaterials : tunedMaterials[0];
    });

    const bounds = new THREE.Box3().setFromObject(source);
    const size = bounds.getSize(new THREE.Vector3());
    const center = bounds.getCenter(new THREE.Vector3());

    source.position.x -= center.x;
    source.position.z -= center.z;
    source.position.y -= bounds.min.y;
    source.updateMatrixWorld(true);

    return {
        model: source,
        size,
    };
}

function findBestOrthogonalRotation(object3d) {
    const rotations = [0, Math.PI / 2, Math.PI, Math.PI * 1.5];
    let bestScore = Number.POSITIVE_INFINITY;
    let bestRotation = new THREE.Euler();

    // Try all right-angle rotations and keep the one where X is longest and Y is smallest.
    for (const x of rotations) {
        for (const y of rotations) {
            for (const z of rotations) {
                const clone = object3d.clone(true);
                clone.rotation.set(x, y, z);
                clone.updateMatrixWorld(true);

                const size = new THREE.Box3().setFromObject(clone).getSize(new THREE.Vector3());
                const longest = Math.max(size.x, size.y, size.z);
                const shortest = Math.min(size.x, size.y, size.z);
                const middle = size.x + size.y + size.z - longest - shortest;
                const score = Math.abs(longest - size.x) + Math.abs(shortest - size.y) + Math.abs(middle - size.z);

                if (score < bestScore) {
                    bestScore = score;
                    bestRotation = new THREE.Euler(x, y, z);
                }
            }
        }
    }

    return bestRotation;
}

function tuneMaterial(material) {
    if ('map' in material && !material.map) {
        material.map = colorMap;
    }

    if ('map' in material && material.map) {
        material.map.anisotropy = renderer.capabilities.getMaxAnisotropy();
    }

    if ('normalMap' in material && !material.normalMap) {
        material.normalMap = normalMap;
        material.normalScale = new THREE.Vector2(0.6, 0.6);
    }

    if ('roughness' in material) {
        material.roughness = 0.88;
    }

    if ('metalness' in material) {
        material.metalness = 0.03;
    }

    if ('shininess' in material) {
        material.shininess = 22;
    }

    material.needsUpdate = true;

    return material;
}

function createBeam({ name, preparedModel, length, width, height, position, rotationY }) {
    const beam = new THREE.Group();
    const instance = preparedModel.model.clone(true);

    instance.traverse((child) => {
        if (!child.isMesh) {
            return;
        }

        const materials = Array.isArray(child.material) ? child.material : [child.material];
        const clonedMaterials = cloneMaterials(materials);
        child.material = Array.isArray(child.material) ? clonedMaterials : clonedMaterials[0];
    });

    instance.scale.set(
        length / preparedModel.size.x,
        height / preparedModel.size.y,
        width / preparedModel.size.z,
    );

    beam.name = name;
    beam.position.copy(position);
    beam.rotation.y = rotationY;
    beam.add(instance);
    illustrationGroup.add(beam);

    return beam;
}

function buildDimensions() {
    addDimension({
        text: '2m',
        anchorStart: point(worldExtents.leftBase.minX, worldExtents.leftBase.maxY, worldExtents.leftBase.maxZ),
        anchorEnd: point(worldExtents.leftBase.minX, worldExtents.leftBase.maxY, worldExtents.leftBase.minZ),
        start: point(worldExtents.leftBase.minX - 0.07, worldExtents.leftBase.maxY + 0.01, worldExtents.leftBase.maxZ),
        end: point(worldExtents.leftBase.minX - 0.07, worldExtents.leftBase.maxY + 0.01, worldExtents.leftBase.minZ),
        labelOffset: new THREE.Vector3(-0.04, 0.02, 0),
    });

    addDimension({
        text: '2m',
        anchorStart: point(worldExtents.topBeam.maxX, worldExtents.topBeam.maxY, worldExtents.topBeam.maxZ),
        anchorEnd: point(worldExtents.topBeam.maxX, worldExtents.topBeam.maxY, worldExtents.topBeam.minZ),
        start: point(worldExtents.topBeam.maxX + 0.05, worldExtents.topBeam.maxY + 0.015, worldExtents.topBeam.maxZ),
        end: point(worldExtents.topBeam.maxX + 0.05, worldExtents.topBeam.maxY + 0.015, worldExtents.topBeam.minZ),
        labelOffset: new THREE.Vector3(0.03, 0.025, 0),
    });

    addDimension({
        text: '3m',
        anchorStart: point(worldExtents.rightBase.minX, worldExtents.rightBase.maxY, worldExtents.rightBase.maxZ),
        anchorEnd: point(worldExtents.rightBase.maxX, worldExtents.rightBase.maxY, worldExtents.rightBase.maxZ),
        start: point(worldExtents.rightBase.minX, worldExtents.rightBase.maxY + 0.015, worldExtents.rightBase.maxZ + 0.08),
        end: point(worldExtents.rightBase.maxX, worldExtents.rightBase.maxY + 0.015, worldExtents.rightBase.maxZ + 0.08),
        labelOffset: new THREE.Vector3(0, 0.03, 0.04),
    });

    addDimension({
        text: '0.5m',
        anchorStart: point(worldExtents.leftBase.minX, worldExtents.leftBase.minY, worldExtents.leftBase.maxZ),
        anchorEnd: point(worldExtents.leftBase.maxX, worldExtents.leftBase.minY, worldExtents.leftBase.maxZ),
        start: point(worldExtents.leftBase.minX, worldExtents.leftBase.minY - 0.02, worldExtents.leftBase.maxZ + 0.06),
        end: point(worldExtents.leftBase.maxX, worldExtents.leftBase.minY - 0.02, worldExtents.leftBase.maxZ + 0.06),
        labelOffset: new THREE.Vector3(0, 0.01, 0.02),
    });

    addDimension({
        text: '0.5m',
        anchorStart: point(worldExtents.rightBase.maxX, worldExtents.rightBase.minY, worldExtents.rightBase.minZ),
        anchorEnd: point(worldExtents.rightBase.maxX, worldExtents.rightBase.minY, worldExtents.rightBase.maxZ),
        start: point(worldExtents.rightBase.maxX + 0.04, worldExtents.rightBase.minY - 0.02, worldExtents.rightBase.minZ),
        end: point(worldExtents.rightBase.maxX + 0.04, worldExtents.rightBase.minY - 0.02, worldExtents.rightBase.maxZ),
        labelOffset: new THREE.Vector3(0.02, 0.01, 0),
    });

    addDimension({
        text: '0.4m',
        anchorStart: point(worldExtents.topBeam.minX, worldExtents.topBeam.minY, worldExtents.topBeam.maxZ),
        anchorEnd: point(worldExtents.topBeam.maxX, worldExtents.topBeam.minY, worldExtents.topBeam.maxZ),
        start: point(worldExtents.topBeam.minX, worldExtents.topBeam.minY - 0.01, worldExtents.topBeam.maxZ + 0.035),
        end: point(worldExtents.topBeam.maxX, worldExtents.topBeam.minY - 0.01, worldExtents.topBeam.maxZ + 0.035),
        labelOffset: new THREE.Vector3(0, 0.025, 0.015),
    });

    addDimension({
        text: '0.2m',
        anchorStart: point(worldExtents.leftBase.minX, worldExtents.leftBase.minY, worldExtents.leftBase.maxZ),
        anchorEnd: point(worldExtents.leftBase.minX, worldExtents.leftBase.maxY, worldExtents.leftBase.maxZ),
        start: point(worldExtents.leftBase.minX - 0.05, worldExtents.leftBase.minY, worldExtents.leftBase.maxZ + 0.02),
        end: point(worldExtents.leftBase.minX - 0.05, worldExtents.leftBase.maxY, worldExtents.leftBase.maxZ + 0.02),
        labelOffset: new THREE.Vector3(-0.01, 0, 0.01),
    });

    addDimension({
        text: '0.2m',
        anchorStart: point(worldExtents.topBeam.maxX, worldExtents.topBeam.minY, worldExtents.topBeam.maxZ),
        anchorEnd: point(worldExtents.topBeam.maxX, worldExtents.topBeam.maxY, worldExtents.topBeam.maxZ),
        start: point(worldExtents.topBeam.maxX + 0.045, worldExtents.topBeam.minY, worldExtents.topBeam.maxZ + 0.02),
        end: point(worldExtents.topBeam.maxX + 0.045, worldExtents.topBeam.maxY, worldExtents.topBeam.maxZ + 0.02),
        labelOffset: new THREE.Vector3(0.012, 0, 0.01),
    });

    addDimension({
        text: '0.2m',
        anchorStart: point(worldExtents.rightBase.maxX, worldExtents.rightBase.minY, worldExtents.rightBase.maxZ),
        anchorEnd: point(worldExtents.rightBase.maxX, worldExtents.rightBase.maxY, worldExtents.rightBase.maxZ),
        start: point(worldExtents.rightBase.maxX + 0.06, worldExtents.rightBase.minY, worldExtents.rightBase.maxZ + 0.015),
        end: point(worldExtents.rightBase.maxX + 0.06, worldExtents.rightBase.maxY, worldExtents.rightBase.maxZ + 0.015),
        labelOffset: new THREE.Vector3(0.014, 0, 0),
    });

    addDimension({
        text: '0.02m',
        anchorStart: point(worldExtents.leftBase.minX, worldExtents.topBeam.minY, worldExtents.leftBase.maxZ - 0.02),
        anchorEnd: point(worldExtents.topBeam.minX, worldExtents.topBeam.minY, worldExtents.leftBase.maxZ - 0.02),
        start: point(worldExtents.leftBase.minX, worldExtents.topBeam.minY + 0.01, worldExtents.leftBase.maxZ - 0.08),
        end: point(worldExtents.topBeam.minX, worldExtents.topBeam.minY + 0.01, worldExtents.leftBase.maxZ - 0.08),
        labelOffset: new THREE.Vector3(0, 0.015, -0.02),
    });
}

function addDimension({ text, anchorStart, anchorEnd, start, end, labelOffset = new THREE.Vector3() }) {
    const dimensionMaterial = new THREE.LineBasicMaterial({ color: 0xffffff });
    addLine(anchorStart, start, dimensionMaterial);
    addLine(anchorEnd, end, dimensionMaterial);
    addLine(start, end, dimensionMaterial);
    addMarker(start);
    addMarker(end);

    const label = document.createElement('div');
    label.className = 'dimension-label';
    label.textContent = text;
    dimensionLayer.appendChild(label);

    labelEntries.push({
        element: label,
        start,
        end,
        offset: labelOffset,
    });
}

function addLine(start, end, material) {
    const geometry = new THREE.BufferGeometry().setFromPoints([start, end]);
    const line = new THREE.Line(geometry, material);
    dimensionGroup.add(line);
}

function cloneMaterials(materials) {
    return materials.map((material) => material.clone());
}

function addMarker(position) {
    const marker = new THREE.Mesh(
        new THREE.BoxGeometry(0.035, 0.035, 0.035),
        new THREE.MeshBasicMaterial({ color: 0xffffff }),
    );

    marker.position.copy(position);
    dimensionGroup.add(marker);
}

function point(x, y, z) {
    return new THREE.Vector3(x, y, z);
}

function animate() {
    requestAnimationFrame(animate);
    controls.update();
    updateLabels();
    renderer.render(scene, camera);
}

function updateLabels() {
    for (const entry of labelEntries) {
        const anchor = entry.start.clone().lerp(entry.end, 0.5).add(entry.offset);
        const projectedAnchor = anchor.clone().project(camera);
        const projectedStart = entry.start.clone().project(camera);
        const projectedEnd = entry.end.clone().project(camera);

        if (projectedAnchor.z < -1 || projectedAnchor.z > 1) {
            entry.element.style.display = 'none';
            continue;
        }

        const anchorX = (projectedAnchor.x * 0.5 + 0.5) * VIEWPORT.width;
        const anchorY = (-projectedAnchor.y * 0.5 + 0.5) * VIEWPORT.height;
        const startX = (projectedStart.x * 0.5 + 0.5) * VIEWPORT.width;
        const startY = (-projectedStart.y * 0.5 + 0.5) * VIEWPORT.height;
        const endX = (projectedEnd.x * 0.5 + 0.5) * VIEWPORT.width;
        const endY = (-projectedEnd.y * 0.5 + 0.5) * VIEWPORT.height;
        const angle = Math.atan2(endY - startY, endX - startX) * (180 / Math.PI);

        entry.element.style.display = 'block';
        entry.element.style.left = `${anchorX}px`;
        entry.element.style.top = `${anchorY}px`;
        entry.element.style.transform = `translate(-50%, -50%) rotate(${angle}deg)`;
    }
}
