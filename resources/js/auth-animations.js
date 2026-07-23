import * as THREE from 'three';
import { animate } from 'motion';

const canvas = document.getElementById('auth-canvas');
if (!canvas) throw new Error('Canvas element #auth-canvas not found');

const scene = new THREE.Scene();

const camera = new THREE.PerspectiveCamera(75, canvas.clientWidth / canvas.clientHeight, 0.1, 1000);
camera.position.z = 30;

const renderer = new THREE.WebGLRenderer({ canvas, alpha: true, antialias: true });
renderer.setSize(canvas.clientWidth, canvas.clientHeight);
renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));

const particlesGeo = new THREE.BufferGeometry();
const count = 200;
const positions = new Float32Array(count * 3);
const sizes = new Float32Array(count);
const speeds = [];

for (let i = 0; i < count; i++) {
    positions[i * 3] = (Math.random() - 0.5) * 80;
    positions[i * 3 + 1] = (Math.random() - 0.5) * 80;
    positions[i * 3 + 2] = (Math.random() - 0.5) * 40;
    sizes[i] = Math.random() * 3 + 0.5;
    speeds.push({
        x: (Math.random() - 0.5) * 0.02,
        y: (Math.random() - 0.5) * 0.02,
        z: (Math.random() - 0.5) * 0.01,
    });
}

particlesGeo.setAttribute('position', new THREE.BufferAttribute(positions, 3));
particlesGeo.setAttribute('size', new THREE.BufferAttribute(sizes, 1));

const textureCanvas = document.createElement('canvas');
textureCanvas.width = 32;
textureCanvas.height = 32;
const ctx = textureCanvas.getContext('2d');
const gradient = ctx.createRadialGradient(16, 16, 0, 16, 16, 16);
gradient.addColorStop(0, 'rgba(251, 146, 60, 1)');
gradient.addColorStop(0.3, 'rgba(251, 146, 60, 0.6)');
gradient.addColorStop(1, 'rgba(251, 146, 60, 0)');
ctx.fillStyle = gradient;
ctx.fillRect(0, 0, 32, 32);
const texture = new THREE.CanvasTexture(textureCanvas);

const material = new THREE.PointsMaterial({
    size: 0.8,
    map: texture,
    blending: THREE.AdditiveBlending,
    depthWrite: false,
    transparent: true,
    opacity: 0.6,
    color: new THREE.Color('#fb923c'),
});

const particles = new THREE.Points(particlesGeo, material);
scene.add(particles);

const geometry = new THREE.IcosahedronGeometry(1.5, 0);
const meshMaterial = new THREE.MeshBasicMaterial({
    color: '#fb923c',
    wireframe: true,
    transparent: true,
    opacity: 0.15,
});
const mesh = new THREE.Mesh(geometry, meshMaterial);
mesh.position.set(0, 0, -5);
scene.add(mesh);

const geometry2 = new THREE.OctahedronGeometry(1, 0);
const meshMaterial2 = new THREE.MeshBasicMaterial({
    color: '#f97316',
    wireframe: true,
    transparent: true,
    opacity: 0.1,
});
const mesh2 = new THREE.Mesh(geometry2, meshMaterial2);
mesh2.position.set(5, -3, -10);
scene.add(mesh2);

let mouseX = 0, mouseY = 0;
document.addEventListener('mousemove', (e) => {
    mouseX = (e.clientX / window.innerWidth - 0.5) * 2;
    mouseY = (e.clientY / window.innerHeight - 0.5) * 2;
});

function startScene() {
    requestAnimationFrame(startScene);

    const positions = particles.geometry.attributes.position.array;
    for (let i = 0; i < count; i++) {
        positions[i * 3] += speeds[i].x;
        positions[i * 3 + 1] += speeds[i].y;
        positions[i * 3 + 2] += speeds[i].z;

        if (positions[i * 3] > 40) positions[i * 3] = -40;
        if (positions[i * 3] < -40) positions[i * 3] = 40;
        if (positions[i * 3 + 1] > 40) positions[i * 3 + 1] = -40;
        if (positions[i * 3 + 1] < -40) positions[i * 3 + 1] = 40;
    }
    particles.geometry.attributes.position.needsUpdate = true;

    mesh.rotation.x += 0.005;
    mesh.rotation.y += 0.01;
    mesh2.rotation.x += 0.01;
    mesh2.rotation.y += 0.005;

    particles.rotation.x += 0.0005 + mouseY * 0.0005;
    particles.rotation.y += 0.001 + mouseX * 0.001;

    renderer.render(scene, camera);
}

startScene();

window.addEventListener('resize', () => {
    const w = canvas.clientWidth;
    const h = canvas.clientHeight;
    camera.aspect = w / h;
    camera.updateProjectionMatrix();
    renderer.setSize(w, h);
});

const formCard = document.querySelector('.auth-form-card');
if (formCard) {
    animate(formCard, { opacity: [0, 1], y: [30, 0] }, { duration: 0.6, easing: 'ease-out' });
}
