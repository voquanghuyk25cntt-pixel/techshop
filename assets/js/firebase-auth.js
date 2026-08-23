// Firebase Auth + Firestore helper
// Usage:
// 1) On any page include Firebase SDKs then define window.FIREBASE_CONFIG = { apiKey: '...', authDomain: '...', projectId: '...', ... } BEFORE including this file.
// 2) This script exposes helper functions on window: fbInit(), fbOnAuthChange(cb), fbSignIn(email,password), fbRegister(email,password,displayName), fbSignOut(), fbSaveOrder(orderObj)

(async function() {
    if (!window.FIREBASE_CONFIG) {
        console.warn('FIREBASE_CONFIG not found. Firebase auth disabled. Paste your Firebase config into the page to enable authentication.');
        return;
    }

    // Load Firebase modular SDK from CDN if not present
    // Expecting firebase/app, firebase/auth, firebase/firestore globals
    if (typeof firebase === 'undefined' || !firebase.apps) {
        console.error('Firebase SDK not found. Make sure to include Firebase scripts before firebase-auth.js');
        return;
    }

    const app = firebase.initializeApp(window.FIREBASE_CONFIG);
    const auth = firebase.auth();
    const db = firebase.firestore();

    // Expose helper functions
    window.fbOnAuthChange = function(cb) {
        return auth.onAuthStateChanged(cb);
    };

    window.fbSignIn = async function(email, password) {
        try {
            const userCred = await auth.signInWithEmailAndPassword(email, password);
            return { success: true, user: userCred.user };
        } catch (err) {
            return { success: false, error: err.message };
        }
    };

    window.fbRegister = async function(email, password, displayName) {
        try {
            const userCred = await auth.createUserWithEmailAndPassword(email, password);
            if (displayName) {
                await userCred.user.updateProfile({ displayName: displayName });
            }
            return { success: true, user: userCred.user };
        } catch (err) {
            return { success: false, error: err.message };
        }
    };

    window.fbSignOut = async function() {
        await auth.signOut();
    };

    // Save order to Firestore under collection 'orders'
    // orderObj should include items, total, shipping info, etc.
    window.fbSaveOrder = async function(orderObj) {
        const user = auth.currentUser;
        if (!user) throw new Error('Not authenticated');
        const payload = Object.assign({}, orderObj, {
            userId: user.uid,
            userEmail: user.email || null,
            createdAt: firebase.firestore.FieldValue.serverTimestamp()
        });
        const ref = await db.collection('orders').add(payload);
        return ref.id;
    };

    // Small helper to require auth for a page
    window.fbRequireAuth = function(redirectTo) {
        auth.onAuthStateChanged(user => {
            if (!user) {
                window.location.href = redirectTo || '/auth/login.html';
            }
        });
    };

    console.log('Firebase Auth helper initialized');
})();
