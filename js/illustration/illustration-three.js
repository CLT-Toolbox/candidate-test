import * as THREE from 'three';
import { OrbitControls } from 'three/addons/controls/OrbitControls.js';

// ======================================================
// SCENE
// ======================================================

const scene = new THREE.Scene();

scene.background = new THREE.Color(0x111111);

// ======================================================
// CAMERA
// ======================================================

const camera = new THREE.PerspectiveCamera(
    45,
    window.innerWidth / window.innerHeight,
    0.1,
    1000
);

camera.position.set(5, 3, 6);

// ======================================================
// RENDERER
// ======================================================

const renderer = new THREE.WebGLRenderer({
    antialias: true
});

renderer.setSize(
    window.innerWidth,
    window.innerHeight
);

renderer.shadowMap.enabled = false;

document.body.style.margin = '0';

document.body.appendChild(
    renderer.domElement
);

// ======================================================
// CONTROLS
// ======================================================

const controls = new OrbitControls(
    camera,
    renderer.domElement
);

controls.enableDamping = true;

// fokus tengah model
controls.target.set(1.2, 0.3, 0.8);

// ======================================================
// LIGHT
// ======================================================

const ambient = new THREE.AmbientLight(
    0xffffff,
    3
);

scene.add(ambient);

const light = new THREE.DirectionalLight(
    0xffffff,
    1
);

light.position.set(5, 10, 5);

scene.add(light);

// ======================================================
// AXIS RGB
// ======================================================

// MERAH = X
// HIJAU = Y
// BIRU = Z

function createAxis(start, end, color) {

    const material =
        new THREE.LineBasicMaterial({
            color: color
        });

    const geometry =
        new THREE.BufferGeometry()
            .setFromPoints([
                start,
                end
            ]);

    const line =
        new THREE.Line(
            geometry,
            material
        );

    scene.add(line);
}

// X merah
createAxis(
    new THREE.Vector3(0, 0, 0),
    new THREE.Vector3(4, 0, 0),
    0xff0000
);

// Y hijau
createAxis(
    new THREE.Vector3(0, 0, 0),
    new THREE.Vector3(0, 4, 0),
    0x00ff00
);

// Z biru
createAxis(
    new THREE.Vector3(0, 0, 0),
    new THREE.Vector3(0, 0, 4),
    0x0000ff
);

// ======================================================
// TEXTURE
// ======================================================

const loader = new THREE.TextureLoader();

const woodTexture = loader.load(
    './model/wood/wood.fbm/Color_A02.jpg'
);

woodTexture.wrapS = THREE.RepeatWrapping;
woodTexture.wrapT = THREE.RepeatWrapping;

woodTexture.repeat.set(2, 2);

// ======================================================
// MATERIAL
// ======================================================

const woodMaterial =
    new THREE.MeshPhongMaterial({

        map: woodTexture,

        shininess: 5

    });

// ======================================================
// UKURAN BALOK
// ======================================================

const LEFT_P = 3;
const LEFT_L = 0.5;
const LEFT_T = 0.2;

const RIGHT_P = 2;
const RIGHT_L = 0.5;
const RIGHT_T = 0.2;

const TOP_P = 2;
const TOP_L = 0.4;
const TOP_T = 0.2;

// ======================================================
// POSISI BALOK KIRI
// ======================================================

const leftPosX =
    LEFT_P / 2;

const leftPosZ =
    LEFT_L / 2;

// ======================================================
// POSISI BALOK KANAN
// ======================================================

const SIKU_DISTANCE = 0.54;

// ujung kanan balok kanan
const rightEndX =
    LEFT_P - SIKU_DISTANCE;

// posisi tengah X
const rightPosX =
    rightEndX - (RIGHT_P / 2);

// posisi Z
const rightPosZ =
    LEFT_L + (RIGHT_L / 2);

// ======================================================
// POSISI BALOK ATAS
// ======================================================

// ======================================================
// X
// ======================================================

// tengah 2 balok bawah
const centerBottomX =
    (leftPosX + rightPosX) / 2;

// geser sedikit ke kiri
const topPosX =
    centerBottomX - 0.28;

// ======================================================
// Z
// ======================================================

// belakang balok kanan
const rightBackZ =
    rightPosZ + (RIGHT_L / 2);

// gap sesuai gambar
const GAP_Z = 0.02;

// belakang balok atas
const topBackZ =
    rightBackZ + GAP_Z;

// center Z balok atas
const topPosZ =
    topBackZ - TOP_L ;

// ======================================================
// Y
// ======================================================

const topPosY =
    LEFT_T + (TOP_T / 2);

// ======================================================
// GEOMETRY
// ======================================================

const leftGeometry =
    new THREE.BoxGeometry(
        LEFT_P,
        LEFT_T,
        LEFT_L
    );

const rightGeometry =
    new THREE.BoxGeometry(
        RIGHT_P,
        RIGHT_T,
        RIGHT_L
    );

const topGeometry =
    new THREE.BoxGeometry(
        TOP_P,
        TOP_T,
        TOP_L
    );

// ======================================================
// KAYU KIRI
// ======================================================

const leftWood =
    new THREE.Mesh(
        leftGeometry,
        woodMaterial
    );

leftWood.position.set(
    leftPosX,
    LEFT_T / 2,
    leftPosZ
);

scene.add(leftWood);

// ======================================================
// KAYU KANAN
// ======================================================

const rightWood =
    new THREE.Mesh(
        rightGeometry,
        woodMaterial
    );

rightWood.position.set(
    rightPosX,
    RIGHT_T / 2,
    rightPosZ
);

scene.add(rightWood);

// ======================================================
// KAYU ATAS
// ======================================================

const topWood =
    new THREE.Mesh(
        topGeometry,
        woodMaterial
    );

topWood.position.set(
    topPosX,
    topPosY,
    topPosZ
);

scene.add(topWood);

// ======================================================
// EDGE PUTIH
// ======================================================

function addEdges(mesh) {

    const edges =
        new THREE.EdgesGeometry(
            mesh.geometry
        );

    const line =
        new THREE.LineSegments(

            edges,

            new THREE.LineBasicMaterial({
                color: 0xffffff
            })

        );

    line.position.copy(
        mesh.position
    );

    line.rotation.copy(
        mesh.rotation
    );

    scene.add(line);
}

addEdges(leftWood);
addEdges(rightWood);
addEdges(topWood);

// ======================================================
// TEXT LABEL
// ======================================================

function createText(text) {

    const canvas =
        document.createElement('canvas');

    canvas.width = 1024;
    canvas.height = 256;

    const ctx =
        canvas.getContext('2d');

    ctx.fillStyle = 'white';

    ctx.font = 'Bold 70px Arial';

    ctx.textAlign = 'center';

    ctx.fillText(
        text,
        512,
        120
    );

    const texture =
        new THREE.CanvasTexture(
            canvas
        );

    const material =
        new THREE.SpriteMaterial({

            map: texture,

            transparent: true

        });

    const sprite =
        new THREE.Sprite(
            material
        );

    sprite.scale.set(
        2,
        0.6,
        1
    );

    return sprite;
}

// ======================================================
// LABEL KAYU KIRI
// ======================================================

const leftText =
    createText(
        '3m x 0.5m x 0.2m'
    );

leftText.position.set(
    1.5,
    0.5,
    -0.3
);

scene.add(leftText);

// ======================================================
// LABEL KAYU KANAN
// ======================================================

const rightText =
    createText(
        '2m x 0.5m x 0.2m'
    );

rightText.position.set(
    rightPosX,
    0.5,
    1.2
);

scene.add(rightText);

// ======================================================
// LABEL KAYU ATAS
// ======================================================

const topText =
    createText(
        '2m x 0.4m x 0.2m'
    );

topText.position.set(
    topPosX,
    0.8,
    1.1
);

scene.add(topText);

// ======================================================
// TITIK 0
// ======================================================

const point0 =
    new THREE.Mesh(

        new THREE.SphereGeometry(
            0.05,
            32,
            32
        ),

        new THREE.MeshBasicMaterial({
            color: 0xffff00
        })

    );

point0.position.set(
    0,
    0,
    0
);

scene.add(point0);

// ======================================================
// ANIMATE
// ======================================================

function animate() {

    requestAnimationFrame(
        animate
    );

    controls.update();

    renderer.render(
        scene,
        camera
    );
}

animate();

// ======================================================
// RESIZE
// ======================================================

window.addEventListener(
    'resize',
    () => {

        camera.aspect =
            window.innerWidth /
            window.innerHeight;

        camera.updateProjectionMatrix();

        renderer.setSize(
            window.innerWidth,
            window.innerHeight
        );

    }
);